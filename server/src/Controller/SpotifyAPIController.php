<?php
    namespace App\Controller;

    use App\Entity\Service;
    use App\Entity\RequestAPI;
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
        public function connect(Request $request, ServiceRepository $sevice_repository)
        {
            $service = new Service();// refaire une fonction dans le repo service qui permet de get avec le nom du service
            /*$client_id = $service->getIdentifiers();// avant le ;
            $client_secret = $service->getIdentifiers();// apres le ;*/
            $client_id = "CLIENT_ID";
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
            $service = new Service();// refaire une fonction dans le repo service qui permet de get avec le nom du service
            /*$client_id = $service->getIdentifiers();// avant le ;
            $client_secret = $service->getIdentifiers();// apres le ;*/
            $client_id = "CLIENT_ID";
            $client_secret = "CLIENT_SECRET";
            $code = $request->query->get("code");
            $state = $request->query->get("state");
            $redirect_uri = "http://localhost:8000/spotify/get_access_token";
            // Vérifiez la validité de la requête en comparant la valeur de l'état avec la valeur générée précédemment
            if ($state != "17") {
                return new JsonResponse(array("status" => "error"));//Bad request
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
            if (isset(json_decode($result)->access_token)) {
                $_SESSION["spotify"] = json_decode($result)->access_token;
            } else {
                return new JsonResponse(array("status" => "error"));//Bad code
            }
            return new JsonResponse(array("status" => "ok"));
        }

        /**
         * @Route("/spotify/search", name="spotify_api_search")
         */
        public function search(Request $request)
        {// type = par exemple artist/track/album/playlist/etc... et search est la recherche
            $type = $request->query->get("type");
            $search = $request->query->get("search");
            $search = str_replace(" ", "%20", $search);
            $response = $this->sendRequest("search?type=$type&q=$search");
            return new JsonResponse($response);
        }
        private function sendRequest($endpoint, $method = "GET", $parameters = array())
        {
            if (empty($_SESSION["spotify"])) {
                return json_encode(array("a"));
            }
            if (empty($this->request_api)) {
                $this->request_api = new RequestAPI();
            }
            $response = $this->request_api->send($_SESSION["spotify"], self::API_URL.$endpoint, $method, $parameters);
            if (isset(json_decode($response)->error)) {
                switch (json_decode($response)->error->status) {
                    case 400:
                        // mauvaise requete (regarder en details la diff avec la 404)
                        $response = array("status" => "error");
                        break;
                    case 401:
                        // mauvais token ou token expire -> re authentifier le user
                        $response = array("status" => "error");
                        break;
                    case 403:
                        // le lien pour la requete de loauth est pas bonne (cle ou valeur) -> refaire le lien de la requete
                        $response = array("status" => "error");
                        break;
                    case 404:
                        // objet rechercher pas trouvé -> id pas existante...
                        $response = array("status" => "error");
                        break;
                    case 429:
                        // trop de requete, jsp comment le corriger
                        $response = array("status" => "error");
                        break;
                    case 500:
                        // une erreur du cote spotify (askip ca ne devrait jamais arriver)
                        $response = array("status" => "error");
                        break;
                    case 502:
                        // bad gateway, jsp trop ce que c'est
                        $response = array("status" => "error");
                        break;
                    case 503:
                        // le serveur spotify est indispo
                        $response = array("status" => "error");
                        break;
                    default:
                        break;
                }
            }
            return $response;
        }

        // faire une fct private pour la fct getRandomMusic en dessous
        // public function search($type, $search)
        // {// type = par exemple artist/track/album/playlist/etc... et search est la recherche
            
        //     $search = str_replace(" ", "%20", $search);
        //     $response = $this->sendRequest("search?type=$type&q=$search");
        //     return new JsonResponse($response);
        // }

        // private function getRandomMusicFromArtist($artist_name)
        // {
        //     srand(time());
        //     $alphabet = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
        //     $response = json_decode($this->search("track", $artist_name." ".$alphabet[rand(0, count($alphabet) - 1)]));
        //     $tracks = $response->tracks->items;
        //     return $tracks[rand(0, count($tracks) - 1)]->uri;
        // }


        // Action
        // public function isMusicAddedToPlaylist($action_id)
        // {// en bdd = playlistId
        //     ;
        // }

/*
        // Reaction
        public function addMusicFromArtistToQueue($reaction_id)
        {// en bdd = artistName
            $artist_name = "";// recuperer artistName en bdd avec symphony
            $music_uri = $this->getRandomMusicFromArtist($artist_name)->uri;
            $parameters = array(
                "uri" => $music_uri
            );
            $this->sendRequest("me/player/queue", "POST", $parameters);
        }
        public function addMusicFromArtistListToPlaylist($reaction_id)
        {// en bdd = artistName;artistName:playlistId
            srand(time());
            $description = "";// recuperer la liste dartiste en bdd et la playlist id avec symphony
            // parser la liste
            $args = explode(":", $description);
            if (count($args) != 2) {
                return;// peut etre throw une erreur
            }
            $artist_list = explode(";", $args[0]);
            $playlist_id = $args[1];
            $music_uri = $this->getRandomMusicFromArtist($artist_list[rand(0, count($artist_list) - 1)])->uri;
            $parameters = array(
                "uri" => $music_uri
            );
            $this->sendRequest("playlists/$playlist_id/tracks", "POST", $parameters);
        }*/
    }
?>