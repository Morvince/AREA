<?php
    require_once("request.class.php");

    class SpotifyAPI {
        private const API_URL = "https://api.spotify.com/v1/";

        private $client_id;
        private $client_secret;
        private $access_token;
        private $request;

        public function __construct($client_id, $client_secret) {
            $this->client_id = $client_id;
            $this->client_secret = $client_secret;
            $this->access_token = "";
            $this->request = new Request();
        }

        // Getter
        public function getClientID() {
            return $this->client_id;
        }
        public function getClientSecret() {
            return $this->client_secret;
        }

        // Setter
        public function setAccessToken($access_token) {
            $this->access_token = $access_token;
        }

        private function sendRequest($endpoint, $method = "GET", $parameters = array()) {
            try {
                $response = $this->request->send($this->access_token, self::API_URL.$endpoint, $method, $parameters);
            } catch (Exception $e) {
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

        public function search($type, $search) {// type = par exemple artist/track/album/playlist/etc... et search est la recherche
            $search = str_replace(" ", "%20", $search);
            $response = $this->sendRequest("search?type=$type&q=$search");
            return $response;
        }

        public function getRandomMusicFromArtist($artist_name) {
            srand(time());
            $t = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
            $response = $this->search("track", $artist_name." ".$t[rand(0, count($t) - 1)]);
            // faire un rand sur les recherches et obtenir la track uri
            $tracks = $response->tracks->items;
            return $tracks[rand(0, count($tracks) - 1)]->uri;
        }

        // Action
        public function isMusicFromArtistStarted() {// en bdd = artistId
            ;
        }

        // Reaction
        public function addMusicFromArtistToQueue() {// en bdd = artistName
            $artist_name = "";// recuperer artistName en bdd avec symphony
            $music_uri = $this->getRandomMusicFromArtist()->uri;
            $parameters = array(
                "uri" => $music_uri
            );
            $this->sendRequest("me/player/queue", "POST", $parameters);
        }
        public function addMusicFromArtistListToPlaylist() {// en bdd = artistName;artistName:playlistId
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
        }
    }
?>