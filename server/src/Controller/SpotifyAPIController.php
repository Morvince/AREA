<?php
    namespace App\Controller;

    use App\Entity\RequestAPI;
    use App\Repository\AutomationActionRepository;
    use App\Repository\ServiceRepository;
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
        public function connect(ServiceRepository $sevice_repository)
        {
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
            $redirect_uri = "http://localhost:8000/spotify/get_access_token";
            return $this->redirectToAutorisationLink($client_id, $redirect_uri);
        }
        private function redirectToAutorisationLink($client_id, $redirect_uri)
        {
            $scope = array( "user-read-playback-state", "user-modify-playback-state", "user-read-currently-playing",
                            "app-remote-control", "streaming",
                            "playlist-read-private", "playlist-read-collaborative", "playlist-modify-private", "playlist-modify-public",
                            "user-follow-modify", "user-follow-read",
                            "user-read-playback-position", "user-top-read", "user-read-recently-played",
                            "user-library-modify", "user-library-read",
                            "user-read-email", "user-read-private"
                        );
            $scope = implode(" ", $scope);
            $state = "17";
            $authorization_url = "https://accounts.spotify.com/authorize?client_id=$client_id&response_type=code&redirect_uri=$redirect_uri&scope=$scope&state=$state";
            return $this->redirect($authorization_url);
        }
        /**
         * @Route("/spotify/get_access_token", name="spotify_api_get_access_token")
         */
        public function getAccessToken(Request $request, ServiceRepository $sevice_repository)
        {
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
            $code = $request->query->get("code");
            $state = $request->query->get("state");
            $redirect_uri = "http://localhost:8000/spotify/get_access_token";
            if ($state != "17") {
                return new JsonResponse(array("message" => "Spotify: Bad request to get access token"), 400);
            }
            // Demande de jeton d'accès
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://accounts.spotify.com/api/token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=$code&redirect_uri=$redirect_uri");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$client_secret");
            $headers = array();
            $headers[] = "Content-Type: application/x-www-form-urlencoded";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            curl_close ($ch);
            if (!isset(json_decode($result)->access_token)) {
                return new JsonResponse(array("message" => "Spotify: Bad code to get access token"), 400);
            }
            return new JsonResponse(array("spotify_token" => json_decode($result)->access_token), 200);
        }


        /**
         * @Route("/spotify/search", name="spotify_api_search")
         */
        public function search(Request $request)
        {// type = par exemple artist/track/album/playlist/etc... et search est la recherche
            if (empty($request->query->get("access_token")) || empty($request->query->get("type")) || empty($request->query->get("search"))) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $access_token = $request->query->get("access_token");
            $type = $request->query->get("type");
            $search = $request->query->get("search");
            $search = str_replace(" ", "%20", $search);
            $response = $this->sendRequest($access_token, "search?type=$type&q=$search");
            if (isset(json_decode($response)->code)) {
                return new JsonResponse(array("message" => json_decode($response)->message), json_decode($response)->code);
            }
            return new JsonResponse($response, 200);
        }
        /**
         * @Route("/spotify/get_user_playlists", name="spotify_get_user_playlists")
         */
        public function getUserPlaylists(Request $request)
        {
            if (empty($request->query->get("access_token"))) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $access_token = $request->query->get("access_token");
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
        public function isMusicAddedToPlaylist()//jsp trop comment implementer car je dois avoir ancienne version de la playlist en plus de la nouvelle
        {// en db = playlist_id
            ;
        }

        // Reaction
        public function changePlaylistDetails($access_token, $automation_action_id, AutomationActionRepository $automation_action_repository)
        {// en db = name:public(true/false):description;playlist_id
            $automation_action = $automation_action_repository->findById($automation_action_id);
            if (empty($automation_action)) {
                return json_encode(array("message" => "Spotify: automation_action_id not found", "code" => 404));
            }
            $informations = $automation_action[0]->getInformations();
            $args = explode(":", $informations);
            if (count($args) != 2) {
                return json_encode(array("message" => "Spotify: Informations error", "code" => 422));
            }
            $data = explode(";", $args[0]);
            $playlist = json_decode($this->getPlaylistById($access_token, $args[1]));
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
            return $this->sendRequest("playlists/$playlist->id?name=&public=&description=", "PUT", $parameters);
        }
        public function addMusicFromArtistListToPlaylist($access_token, $automation_action_id, AutomationActionRepository $automation_action_repository)
        {// en db = artist_id;artist_id:playlist_id
            srand(time());
            $automation_action = $automation_action_repository->findById($automation_action_id);
            if (empty($automation_action)) {
                return json_encode(array("message" => "Spotify: automation_action_id not found", "code" => 404));
            }
            $informations = $automation_action[0]->getInformations();
            $args = explode(":", $informations);
            if (count($args) != 2) {
                return json_encode(array("message" => "Spotify: Informations error", "code" => 422));
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
                return json_encode(array("message" => "Spotify: artist_id not found", "code" => 404));
            }
            $playlist_id = $args[1];
            $music_uri = $this->getRandomMusicFromArtist($access_token, $artists_name[rand(0, count($artists_name) - 1)]);
            return $this->sendRequest("playlists/$playlist_id/tracks?uris=$music_uri", "POST");
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