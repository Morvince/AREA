<?php
    namespace App\Controller;

    use App\Entity\RequestAPI;
    use App\Entity\UserService;
    use App\Repository\AutomationRepository;
    use App\Repository\AutomationActionRepository;
    use App\Repository\ServiceRepository;
    use App\Repository\UserRepository;
    use App\Repository\UserServiceRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;

    class SpotifyAPIController extends AbstractController
    {
        private const API_URL = "https://api.spotify.com/v1/";
        private RequestAPI $request_api;

        /**
         * @brief This function is used to connect the service with the user account 
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when the action is working
        */

        /**
         * @Route("/spotify/connect", name="spotify_api_connect")
         */
        public function connect(Request $request, ServiceRepository $service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->redirect_uri)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $redirect_uri = $request_content->redirect_uri;
            $service = $service_repository->findByName("spotify");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Spotify: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (empty($identifiers)) {
                return new JsonResponse(array("message" => "Spotify: Identifiers error"), 422);
            }
            $client_id = $identifiers[0];
            // Compose the authorization scope
            $scope = array(
                "user-read-playback-state", "user-modify-playback-state", "user-read-currently-playing",
                "app-remote-control", "streaming",
                "playlist-read-private", "playlist-read-collaborative", "playlist-modify-private", "playlist-modify-public",
                "user-follow-modify", "user-follow-read",
                "user-read-playback-position", "user-top-read", "user-read-recently-played",
                "user-library-modify", "user-library-read",
                "user-read-email", "user-read-private"
            );
            $scope = implode(" ", $scope);
            // Set the state when the request is good
            $state = "17";
            // Compose the authorization url
            $authorization_url = "https://accounts.spotify.com/authorize?client_id=$client_id&response_type=code&redirect_uri=$redirect_uri&scope=$scope&state=$state";
            return new JsonResponse(array("authorization_url" => $authorization_url), 200);
        }

        /**
         * @brief This function is used to get the access_token with the OAuth request
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when the action is working
         */

        /**
         * @Route("/spotify/get_access_token", name="spotify_api_get_access_token")
         */
        public function getAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->state)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $state = $request_content->state;
            if ($state != "17") {
                return new JsonResponse(array("message" => "Spotify: Bad request to get access token"), 400);
            }
            if (empty($request_content->token) || empty($request_content->redirect_uri) || empty($request_content->code)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Spotify: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user_id = $user->getId();
            $code = $request_content->code;
            $redirect_uri = $request_content->redirect_uri;
            $service = $service_repository->findByName("spotify");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Spotify: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 2) {
                return new JsonResponse(array("message" => "Spotify: Identifiers error"), 422);
            }
            $client_id = $identifiers[0];
            $client_secret = $identifiers[1];
            // Request for the access token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://accounts.spotify.com/api/token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=$code&redirect_uri=$redirect_uri");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$client_secret");
            $headers = array();
            $headers[] = "Content-Type: application/x-www-form-urlencoded";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            curl_close($ch);
            if (!isset(json_decode($result)->access_token)) {
                return new JsonResponse(array("message" => "Spotify: Bad code to get access token"), 400);
            }
            // Put or edit datas in database
            if (empty($user_service_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                $user_service = new UserService();
                $user_service->setUserId($user_id);
                $user_service->setServiceId($service->getId());
            } else {
                $user_service = $user_service_repository->findByUserIdAndServiceId($user_id, $service->getId())[0];
            }
            $user_service->setAccessToken(json_decode($result)->access_token);
            $user_service->setRefreshToken(json_decode($result)->refresh_token);
            $user_service_repository->add($user_service, true);
            return new JsonResponse(array("message" => "OK", 200));
        }

        /**
         * @brief This function is used to set the new refresh token for the user
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when the action is working
         */

        /**
         * @Route("/spotify/refresh_access_token", name="spotify_api_refresh_access_token")
         */
        public function refreshAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->access_token)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $access_token = $request_content->access_token;
            $service = $service_repository->findByName("spotify");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Spotify: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 2) {
                return new JsonResponse(array("message" => "Spotify: Identifiers error"), 422);
            }
            if (empty($user_service_repository->findBy(array("access_token" => $access_token)))) {
                return new JsonResponse(array("message" => "Spotify: Refresh token not found"), 404);
            }
            $client_id = $identifiers[0];
            $client_secret = $identifiers[1];
            $user_service = $user_service_repository->findBy(array("access_token" => $access_token))[0];
            $refresh_token = $user_service->getRefreshToken();
            // Request for the access token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://accounts.spotify.com/api/token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=refresh_token&refresh_token=$refresh_token&client_id=$client_id&client_secret=$client_secret");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$client_secret");
            $headers = array();
            $headers[] = "Content-Type: application/x-www-form-urlencoded";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            curl_close($ch);
            if (empty(json_decode($result)->access_token)) {
                $user_service_repository->remove($user_service, true);
                return new JsonResponse(array("message" => "Spotify: Expired refresh token"), 400);
            }
            // Edit datas in database
            $user_service->setAccessToken(json_decode($result)->access_token);
            $user_service_repository->add($user_service, true);
            return new JsonResponse(array("message" => "OK"), 200);
        }

        /**
         * @brief This function is used to get if the service is connected and currently working
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when the action is working
         */

        /**
         * @Route("/spotify/connected", name="spotify_api_connected")
         */
        public function isConnected(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->token)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Spotify: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user_id = $user->getId();
            $service = $service_repository->findByName("spotify");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Spotify: Service not found"), 404);
            }
            $service = $service[0];
            if (empty($user_service_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                return new JsonResponse(array("connected" => false), 200);
            }
            return new JsonResponse(array("connected" => true), 200);
        }

        /**
         * @brief This function is used to allow user to make a search with the api
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when the action is working
         */

        /**
         * @Route("/spotify/search", name="spotify_api_search")
         */
        public function search(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            if (empty($request->query->get("token")) || empty($request->query->get("type")) || empty($request->query->get("search"))) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $token = $request->query->get("token");
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Spotify: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user_id = $user->getId();
            $service = $service_repository->findByName("spotify");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Spotify: Service not found"), 404);
            }
            $service = $service[0];
            if (empty($user_service_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                return new JsonResponse(array("message" => "Spotify: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($user_id, $service->getId())[0]->getAccessToken();
            $type = $request->query->get("type");
            $search = $request->query->get("search");
            $search = str_replace(" ", "%20", $search);
            // Request for the search
            $response = $this->sendRequest($access_token, "search?type=$type&q=$search");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            $formatted = array();
            foreach ($response[$type."s"]->items as $item) {
                array_push($formatted, array("name" => $item->name, "id" => $item->id));
            }
            return new JsonResponse(array("items" => $formatted), 200);
        }

         /**
         * @brief This function is used to get the user playlist
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when the action is working
         */

        /**
         * @Route("/spotify/get_user_playlists", name="spotify_api_get_user_playlists")
         */
        public function getUserPlaylists(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->token)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Spotify: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user_id = $user->getId();
            $service = $service_repository->findByName("spotify");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Spotify: Service not found"), 404);
            }
            $service = $service[0];
            if (empty($user_service_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                return new JsonResponse(array("message" => "Spotify: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($user_id, $service->getId())[0]->getAccessToken();
            // Request for the user playlists
            $response = $this->sendRequest($access_token, "me/playlists"); // changer pour voir seulement celles modifiables
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            if (empty($response->items)) {
                return new JsonResponse(array("message" => $response), 500);
            }
            $formatted = array();
            foreach ($response->items as $item) {
                array_push($formatted, array("name" => $item->name, "id" => $item->id));
            }
            return new JsonResponse(array("items" => $formatted), 200);
        }

        private function sendRequest($access_token, $endpoint, $method = "GET", $parameters = array())
        {
            if (empty($this->request_api)) {
                $this->request_api = new RequestAPI();
            }
            $response = json_decode($this->request_api->send($access_token, self::API_URL . $endpoint, $method, $parameters));
            if (isset($response->error)) {
                if ($response->error->status === 403 || str_contains($response->error->message, "access token expired")) {
                    $response = json_decode($this->request_api->sendRoute("http://localhost/spotify/refresh_access_token", array("access_token" => $access_token)));
                    if (isset($response->code)) {
                        $response = array("message" => "Spotify: Refresh token error", "code" => $response->code);
                    } else {
                        return $this->sendRequest($access_token, $endpoint, $method, $parameters);
                    }
                } else {
                    $response = array("message" => "Spotify: ".$response->error->message, "code" => $response->error->status);
                }
            }
            return json_decode(json_encode($response));
        }

        // Action

        /**
         * @brief This function is used to get the music playlist changes and trigger reaction if verified
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @return JsonReponse will return a Json object containing all the information when working
         */

        /**
         * @Route("/spotify/action/check_music_playlist", name="spotify_api_action_check_music_playlist")
         */
        public function isMusicAddedToPlaylist(Request $request)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->new) || empty($request_content->old)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $old_tracks = $request_content->old;
            $new_tracks = $request_content->new;
            // Check if tracks have been added to playlist
            foreach ($new_tracks as $new_track) {
                $found = false;
                foreach ($old_tracks as $old_track) {
                    if (strcmp($new_track->track->id, $old_track->track->id) === 0) {
                        $found = true;
                        break (1);
                    }
                }
                if ($found === false) {
                    return new JsonResponse(array("message" => true), 200);
                }
            }
            return new JsonResponse(array("message" => false), 200);
        }

        /**
         * @brief This function is used to get the parameters for the check music action
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when working
         */

        /**
         * @Route("/spotify/action/check_music_playlist/get_parameters", name="spotify_api_action_check_music_playlist_parameters")
         */
        public function getIsMusicAddedToPlaylistParameters(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Spotify: automation_action ID not found"), 404);
            }
            $service = $service_repository->findByName("spotify");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Spotify: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Spotify: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            $informations = $automation_action->getInformations();
            if (empty($informations->playlist_id)) {
                return new JsonResponse(array("message" => "Spotify: Playlist ID not found"), 404);
            }
            // Request to get the wished playlist
            $playlist = $this->getPlaylistById($access_token, $informations->playlist_id);
            if (!empty($playlist->code)) {
                return new JsonResponse(array("message" => $playlist->message), $playlist->code);
            }
            return new JsonResponse($playlist->tracks->items, 200);
        }

        // Reaction

        /**
         * @brief This function is used to change the playlist details in spotify
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when working
         */

        /**
         * @Route("/spotify/reaction/change_playlist_details", name="spotify_api_reaction_change_playlist_details")
         */
        public function changePlaylistDetails(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Spotify: automation_action ID not found"), 404);
            }
            $service = $service_repository->findByName("spotify");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Spotify: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Spotify: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            $informations = $automation_action->getInformations();
            if (empty($informations->playlist_id)) {
                return new JsonResponse(array("message" => "Spotify: Playlist ID not found"), 404);
            }
            $playlist = $this->getPlaylistById($access_token, $informations->playlist_id);
            if (isset($playlist->code)) {
                return new JsonResponse(array("message" => $playlist->message), $playlist->code);
            }
            $name = $playlist->name;
            if (!empty($informations->name)) {
                $name = $informations->name;
            }
            $description = $playlist->description;
            if (!empty($informations->description)) {
                $description = $informations->description;
            }
            $parameters = array(
                "name" => $name,
                "description" => $description
            );
            // Request to change playlist details
            $response = $this->sendRequest($access_token, "playlists/$playlist->id?name=&public=&description=", "PUT", $parameters);
            if (!empty($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }

        /**
         * @brief This function is used to add a music in a playlist
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when working
         */

        /**
         * @Route("/spotify/reaction/add_artist_music_to_playlist", name="spotify_api_reaction_add_artist_music_to_playlist")
         */
        public function addMusicFromArtistToPlaylist(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            srand(time());
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Spotify: automation_action ID not found"), 404);
            }
            $service = $service_repository->findByName("spotify");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Spotify: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Spotify: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            $informations = $automation_action->getInformations();
            if (empty($informations->artist)) {
                return new JsonResponse(array("message" => "Spotify: Artists ID not found"), 404);
            }
            if (empty($informations->playlist_id)) {
                return new JsonResponse(array("message" => "Spotify: Playlist ID not found"), 404);
            }
            $playlist_id = $informations->playlist_id;
            $music_uri = $this->getRandomMusicFromArtist($access_token, $informations->artist);
            if (isset($music_uri->code)) {
                return new JsonResponse(array("message" => $music_uri->message), $music_uri->code);
            }
            // Request to add a song to a playlist
            $response = $this->sendRequest($access_token, "playlists/$playlist_id/tracks?uris=$music_uri", "POST");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }

        private function getArtistById($access_token, $artist_id)
        {
            return $this->sendRequest($access_token, "artists/" . $artist_id);
        }

        private function getPlaylistById($access_token, $playlist_id)
        {
            return $this->sendRequest($access_token, "playlists/" . $playlist_id);
        }

        private function getRandomMusicFromArtist($access_token, $artist_name)
        {
            srand(time());
            $alphabet = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
            $response = $this->privateSearch($access_token, "track", $artist_name . " " . $alphabet[rand(0, count($alphabet) - 1)]);
            if (isset($response->code)) {
                return array("message" => $response->message, "code" => $response->code);
            }
            $tracks = $response->tracks->items;
            return $tracks[rand(0, count($tracks) - 1)]->uri;
        }

        private function privateSearch($access_token, $type, $search)
        { // type = par exemple artist/track/album/playlist/etc... et search est la recherche
            $search = str_replace(" ", "%20", $search);
            return $this->sendRequest($access_token, "search?type=$type&q=$search");
        }
    }
?>