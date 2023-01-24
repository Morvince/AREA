<?php
    class Request {
        public function __construct() {}

        public function send($access_token, $url, $method, $parameters)
        {
            $headers = array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Bearer $access_token"//"Authorization: Basic ".base64_encode($clientId.":".$clientSecret)
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            switch ($method) {
                case "POST":
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
                    break;
                case "PUT":
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
                    break;
                case "DELETE":
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                    break;
                default:
                    break;
            }
            $response = curl_exec($ch);
            if (curl_error($ch)) {
                curl_close($ch);
                throw Exception(json_decode($response)->error->message, json_decode($response)->error->status);
            }
            curl_close($ch);
            return json_decode($response);
        }
    }
?>