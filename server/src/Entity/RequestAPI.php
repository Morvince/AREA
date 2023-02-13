<?php
    namespace App\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\MappedSuperclass
     */
    class RequestAPI
    {
        public function __construct() {}

        public function send($access_token, $url, $method, $parameters)
        {
            $headers = array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Bearer $access_token"
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            switch ($method) {
                case "POST":
                    curl_setopt($ch, CURLOPT_POST, true);
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
                return (json_encode(array("message" => json_decode($response)->error->message, "code" => json_decode($response)->error->status)));
            }
            curl_close($ch);
            return ($response);
        }
        public function sendRoute($url, $parameters)
        {
            $headers = array(
                "Accept: application/json",
                "Content-Type: application/json",
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
            $response = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($code > 200) {
                curl_close($ch);
                if (empty(json_decode($response)->message)) {
                    return (json_encode(array("message" => array("error" => json_decode($response)), "code" => $code)));
                }
                $new_response = array("message" => json_decode($response)->message, "code" => $code);
                return (json_encode($new_response));
            }
            curl_close($ch);
            return ($response);
        }
    }
?>