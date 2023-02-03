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
         * @Route("/spotify/connect", name="spotify_api_connect")
         */
        public function connect(Request $request, ServiceRepository $sevice_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->redirect_uri)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $redirect_uri = $request_content->redirect_uri;
            $service = $sevice_repository->findByName("spotify");
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
            $scope = array( "user-read-playback-state", "user-modify-playback-state", "user-read-currently-playing",
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
         * @Route("/spotify/get_access_token", name="spotify_api_get_access_token")
         */
        public function getAccessToken(Request $request, ServiceRepository $sevice_repository, UserRepository $user_repository, UserServiceRepository $user_sevice_repository)
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
            $service = $sevice_repository->findByName("spotify");
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
            if (empty($user_sevice_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                $user_service = new UserService();
                $user_service->setUserId($user_id);
                $user_service->setServiceId($service->getId());
                $user_service->setAccessToken(json_decode($result)->access_token);
                $user_service->setRefreshToken(json_decode($result)->refresh_token);
                $user_sevice_repository->add($user_service, true);
            } else {
                $user_service = $user_sevice_repository->findByUserIdAndServiceId($user_id, $service->getId())[0];
                $user_service->setAccessToken(json_decode($result)->access_token);
                $user_service->setRefreshToken(json_decode($result)->refresh_token);
                $user_sevice_repository->edit($user_service, true);
            }
            return new JsonResponse(array("message" => "OK", 200));
        }
        /**
         * @Route("/spotify/refresh_access_token", name="spotify_api_refresh_access_token")
         */
        public function refreshAccessToken(Request $request, ServiceRepository $sevice_repository, UserServiceRepository $user_sevice_repository)
        {// a changer pour lutiliser que via le server
            // Get needed values
            if (empty($request->query->get("user_id"))) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $user_id = $request->query->get("user_id");
            $service = $sevice_repository->findByName("spotify");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Spotify: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 2) {
                return new JsonResponse(array("message" => "Spotify: Identifiers error"), 422);
            }
            if (empty($user_sevice_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                return new JsonResponse(array("message" => "Spotify: Refresh token not found"), 404);
            }
            $client_id = $identifiers[0];
            $client_secret = $identifiers[1];
            $user_service = $user_sevice_repository->findByUserIdAndServiceId($user_id, $service->getId())[0];
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
            curl_close ($ch);
            if (!isset(json_decode($result)->access_token)) {
                $user_sevice_repository->remove($user_service);
                return new JsonResponse(array("message" => "Spotify: Bad refresh token to get access token"), 400);
            }
            // Edit datas in database
            $user_service->setAccessToken(json_decode($result)->access_token);
            $user_sevice_repository->edit($user_service, true);
            return new JsonResponse(array("message" => "OK"), 200);
        }

        /**
         * @Route("/spotify/search", name="spotify_api_search")
         */
        public function search(Request $request, ServiceRepository $sevice_repository, UserServiceRepository $user_sevice_repository, UserRepository $user_repository)
        {// type = par exemple artist/track/album/playlist/etc... et search est la recherche
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->token) || empty($request_content->type) || empty($request_content->search)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Spotify: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user_id = $user->getId();
            $service = $sevice_repository->findByName("spotify");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Spotify: Service not found"), 404);
            }
            $service = $service[0];
            if (empty($user_sevice_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                return new JsonResponse(array("message" => "Spotify: Access token not found"), 404);
            }
            $access_token = $user_sevice_repository->findByUserIdAndServiceId($user_id, $service->getId())[0]->getAccessToken();
            $type = $request_content->type;
            $search = $request_content->search;
            $search = str_replace(" ", "%20", $search);
            // Request for the search
            $response = $this->sendRequest($access_token, "search?type=$type&q=$search");
            if (isset(json_decode($response)->code)) {
                return new JsonResponse(array("message" => json_decode($response)->message), json_decode($response)->code);
            }
            return new JsonResponse($response, 200);
        }
        /**
         * @Route("/spotify/get_user_playlists", name="spotify_api_get_user_playlists")
         */
        public function getUserPlaylists(Request $request, ServiceRepository $sevice_repository, UserServiceRepository $user_sevice_repository, UserRepository $user_repository)
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
            $service = $sevice_repository->findByName("spotify");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Spotify: Service not found"), 404);
            }
            $service = $service[0];
            if (empty($user_sevice_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                return new JsonResponse(array("message" => "Spotify: Access token not found"), 404);
            }
            $access_token = $user_sevice_repository->findByUserIdAndServiceId($user_id, $service->getId())[0]->getAccessToken();
            // Request for the user playlists
            $response = $this->sendRequest($access_token, "me/playlists");// changer pour voir seulement celles modifiables
            if (isset(json_decode($response)->code)) {
                return new JsonResponse(array("message" => json_decode($response)->message), json_decode($response)->code);
            }
            return new JsonResponse($response, 200);
        }
        private function sendRequest($access_token, $endpoint, $method = "GET", $parameters = array())
        {
            if (empty($this->request_api)) {
                $this->request_api = new RequestAPI();
            }
            $response = $this->request_api->send($access_token, self::API_URL.$endpoint, $method, $parameters);
            if (isset(json_decode($response)->error)) {
                switch (json_decode($response)->error->status) {
                    case 400:
                        $response = json_encode(array("message" => "Spotify: Bad request", "code" => 400));
                        break;
                    case 401:
                        $response = json_encode(array("message" => "Spotify: Bad token or expired", "code" => 401));
                        break;
                    case 403:
                        $response = json_encode(array("message" => "Spotify: Forbidden", "code" => 403));
                        break;
                    case 404:
                        $response = json_encode(array("message" => "Spotify: Ressource not found", "code" => 404));
                        break;
                    case 429:
                        $response = json_encode(array("message" => "Spotify: Too many requests", "code" => 429));
                        break;
                    case 500:
                        $response = json_encode(array("message" => "Spotify: Internal server error", "code" => 500));
                        break;
                    case 502:
                        $response = json_encode(array("message" => "Spotify: Bad gateway", "code" => 502));
                        break;
                    case 503:
                        $response = json_encode(array("message" => "Spotify: Service unavailable", "code" => 503));
                        break;
                    default:
                        break;
                }
            }
            return $response;
        }

        // Action
        /**
         * @Route("/spotify/action/check_music_playlist", name="spotify_api_check_music_playlist")
         */
        public function isMusicAddedToPlaylist(Request $request)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->new) || empty($request_content->old)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $old_tracks = $request_content->old->tracks->items;
            $new_tracks = $request_content->new->tracks->items;
            // Check if tracks have been added to playlist
            foreach ($new_tracks as $new_track) {
                $found = false;
                foreach ($old_tracks as $old_track) {
                    if (strcmp($new_track->track->id, $old_track->track->id) === 0) {
                        $found = true;
                        break(1);
                    }
                }
                if ($found === false) {
                    return new JsonResponse(array("message" => true), 200);
                }
            }
            return new JsonResponse(array("message" => false), 200);
        }
        /**
         * @Route("/spotify/action/check_music_playlist/get_parameters", name="spotify_api_check_music_playlist_parameters")
         */
        public function getIsMusicAddedToPlaylistParameters(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $sevice_repository, UserServiceRepository $user_sevice_repository)
        {// en db = playlist_id
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Spotify: automation_action_id not found"), 404);
            }
            $service = $sevice_repository->findByName("spotify");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Spotify: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_sevice_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Spotify: Access token not found"), 404);
            }
            $access_token = $user_sevice_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            $informations = $automation_action->getInformations();
            // Request to get the wished playlist
            $playlist = json_decode($this->getPlaylistById($access_token, $informations));
            if (isset($playlist->code)) {
                return new JsonResponse(array("message" => $playlist->message), $playlist->code);
            }
            return new JsonResponse($playlist, 200);
        }

        // Reaction
        /**
         * @Route("/spotify/reaction/change_playlist_details", name="spotify_api_reaction_change_playlist_details")
         */
        public function changePlaylistDetails(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $sevice_repository, UserServiceRepository $user_sevice_repository)
        {// en db = name:public(true/false):description;playlist_id
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Spotify: automation_action_id not found"), 404);
            }
            $service = $sevice_repository->findByName("spotify");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Spotify: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_sevice_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Spotify: Access token not found"), 404);
            }
            $access_token = $user_sevice_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            $informations = $automation_action->getInformations();
            $args = explode(":", $informations);
            if (count($args) != 2) {
                return new JsonResponse(array("message" => "Spotify: Informations error"), 422);
            }
            $data = explode(";", $args[0]);
            $playlist = json_decode($this->getPlaylistById($access_token, $args[1]));
            if (isset($playlist->code)) {
                return new JsonResponse(array("message" => $playlist->message), $playlist->code);
            }
            $name = $playlist->name;
            if (!empty($data[0])) {
                $name = $data[0];
            }
            $public = $playlist->public;
            switch ($data[1]) {
                case "false":
                    $public = false;
                    break;
                case "true":
                    $public = true;
                    break;
                default:
                    break;
            }
            $description = $playlist->description;
            if (!empty($data[2])) {
                $description = $data[2];
            }
            $parameters = array(
                "name" => $name,
                "public" => $public,
                "description" => $description
            );
            // Request to change playlist details
            $response = $this->sendRequest($access_token, "playlists/$playlist->id?name=&public=&description=", "PUT", $parameters);
            if (isset(json_decode($response)->code)) {
                return new JsonResponse(array("message" => json_decode($response)->message), json_decode($response)->code);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }
        /**
         * @Route("/spotify/reaction/add_artist_music_to_playlist", name="spotify_api_reaction_add_artist_music_to_playlist")
         */
        public function addMusicFromArtistListToPlaylist(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $sevice_repository, UserServiceRepository $user_sevice_repository)
        {// en db = artist_id;artist_id:playlist_id
            // Get needed values
            srand(time());
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Spotify: automation_action_id not found"), 404);
            }
            $service = $sevice_repository->findByName("spotify");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Spotify: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_sevice_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Spotify: Access token not found"), 404);
            }
            $access_token = $user_sevice_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            $informations = $automation_action->getInformations();
            $args = explode(":", $informations);
            if (count($args) != 2) {
                return new JsonResponse(array("message" => "Spotify: Informations error"), 422);
            }
            $artists_id = explode(";", $args[0]);
            $artists_name = array();
            foreach ($artists_id as $artist_id) {
                if (!empty($artist_id)) {
                    $response = json_decode($this->getArtistById($access_token, $artist_id));
                    if (!empty($response->name)) {
                        array_push($artists_name, $response->name);
                    }
                }
            }
            if (empty($artists_name)) {
                return new JsonResponse(array("message" => "Spotify: artist_id not found"), 404);
            }
            $playlist_id = $args[1];
            $music_uri = $this->getRandomMusicFromArtist($access_token, $artists_name[rand(0, count($artists_name) - 1)]);
            // Request to add a song to a playlist
            $response = $this->sendRequest($access_token, "playlists/$playlist_id/tracks?uris=$music_uri", "POST");
            if (isset(json_decode($response)->code)) {
                return new JsonResponse(array("message" => json_decode($response)->message), json_decode($response)->code);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }
        private function getArtistById($access_token, $artist_id)
        {
            return $this->sendRequest($access_token, "artists/".$artist_id);
        }
        private function getPlaylistById($access_token, $playlist_id)
        {
            return $this->sendRequest($access_token, "playlists/".$playlist_id);
        }
        private function getRandomMusicFromArtist($access_token, $artist_name)
        {
            srand(time());
            $alphabet = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
            $response = json_decode($this->privateSearch($access_token, "track", $artist_name." ".$alphabet[rand(0, count($alphabet) - 1)]));
            $tracks = $response->tracks->items;
            return $tracks[rand(0, count($tracks) - 1)]->uri;
        }
        private function privateSearch($access_token, $type, $search)
        {// type = par exemple artist/track/album/playlist/etc... et search est la recherche
            $search = str_replace(" ", "%20", $search);
            return $this->sendRequest($access_token, "search?type=$type&q=$search");
        }
    }
?>