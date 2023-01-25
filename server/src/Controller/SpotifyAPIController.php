<?php
    namespace App\Controller;

    use App\Entity\Service;
    use App\Entity\RequestAPI;
    use App\Repository\ServiceRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    class SpotifyAPIController extends AbstractController
    {
        private const API_URL = "https://api.spotify.com/v1/";
        private RequestAPI $request = new RequestAPI();

        /**
         * @Route("/spotify/get_access_token", name="spotify_api_get_access_token")
         */
        public function getAccessToken(Request $request, ServiceRepository $sevice_repository)
        {
            $service = new Service();// refaire une fonction dans le repo service qui permet de get avec le nom du service
            $client_id = $service->getIdentifiers();// avant le ;
            $client_secret = $service->getIdentifiers();// apres le ;
            $redirect_uri = "http://localhost:8000/spotify/get_access_token";
            if (empty($request->query->get("code"))) {
                // Génération de l'URL de demande d'autorisation
                $scope = array( "user-read-playback-state", "user-modify-playback-state", "user-read-currently-playing",
                                "app-remote-control", "streaming",
                                "playlist-read-private", "playlist-read-collaborative", "playlist-modify-private", "playlist-modify-public",
                                "user-follow-modify", "user-follow-read",
                                "user-read-playback-position", "user-top-read", "user-read-recently-played",
                                "user-library-modify", "user-library-read",
                                "user-read-email", "user-read-private"
                            ); // Les autorisations requises par votre application
                $scope = implode(" ", $scope);
                $state = "17"; // Un chaîne utilisée pour vérifier la sécurité de la demande d'autorisation
                $authorization_url = "https://accounts.spotify.com/authorize?client_id=$client_id&response_type=code&redirect_uri=$redirect_uri&scope=$scope&state=$state";
                header("Location: $authorization_url");
            }
            // Récupération du code d'autorisation à partir de la requête de redirection
            $code = $request->query->get("code");
            $state = $request->query->get("state");
            // Vérifiez la validité de la requête en comparant la valeur de l'état avec la valeur générée précédemment
            if ($state != "17") {
                echo "La requête d'autorisation n'est pas valide";
                return new Response("Bad request");
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
            if (curl_errno($ch)) {
                echo 'Error:'.curl_error($ch);
                return new Response("Error");
            }
            curl_close ($ch);
            $_SESSION["spotify"] = json_decode($result)->access_token;
            return new Response("Got token");
        }

        private function sendRequest($endpoint, $method = "GET", $parameters = array())
        {
            try {
                $response = $this->request->send($_SESSION["spotify"], self::API_URL.$endpoint, $method, $parameters);
            } catch (\Exception $e) {
                switch ($e->getCode()) {
                    case 400:
                        // mauvaise requete (regarder en details la diff avec la 404)
                        break;
                    case 401:
                        // mauvais token ou token expire -> re authentifier le user
                        break;
                    case 403:
                        // le lien pour la requete de loauth est pas bonne (cle ou valeur) -> refaire le lien de la requete
                        break;
                    case 404:
                        // objet rechercher pas trouvé -> id pas existante...
                        break;
                    case 429:
                        // trop de requete, jsp comment le corriger
                        break;
                    case 500:
                        // une erreur du cote spotify (askip ca ne devrait jamais arriver)
                        break;
                    case 502:
                        // bad gateway, jsp trop ce que c'est
                        break;
                    case 503:
                        // le serveur spotify est indispo
                        break;
                    default:
                        break;
                }
            }
            return $response;
        }

        /**
         * @Route("/spotify/search", name="spotify_api_search")
         */
        public function search($type, $search)
        {// type = par exemple artist/track/album/playlist/etc... et search est la recherche
            $search = str_replace(" ", "%20", $search);
            $response = $this->sendRequest("search?type=$type&q=$search");
            return new Response(json_encode($response));
        }

        private function getRandomMusicFromArtist($artist_name)
        {
            srand(time());
            $alphabet = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
            $response = json_decode($this->search("track", $artist_name." ".$alphabet[rand(0, count($alphabet) - 1)]));
            $tracks = $response->tracks->items;
            return $tracks[rand(0, count($tracks) - 1)]->uri;
        }


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