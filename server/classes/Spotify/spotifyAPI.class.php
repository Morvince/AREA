<?php
    require_once("request.class.php");

    class SpotifyAPI {
        public const API_URL = "https://api.spotify.com/v1/";

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

        public function sendRequest($endpoint, $method = "GET", $parameters = array()) {
            try {
                $response = $this->request->send($this->access_token, self::API_URL.$endpoint, $method, $parameters);
            } catch (Exception $e) {
                switch ($e->getCode()) {
                    case 401:
                        // mauvais token ou token expire -> re authentifier le user
                        break;
                    case 403:
                        // erreur que je comprends pas
                        break;
                    case 429:
                        // erreur que je comprends pas non plus
                        break;
                    default:
                        break;
                }
            }
            return $response;
        }
    }
?>