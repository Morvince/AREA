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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class TwitterAPIController extends AbstractController
{
    private const API_URL = "https://api.twitter.com/2/";
    private RequestAPI $request_api;


    /**
     * @Route("/twitter/test", name="twitter_api_test")
     */
    public function test(Request $request, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository)
    {
        // $parameters = array("automation_action_id" => 2);
        $url = "http://localhost/twitter/connect";
        if (empty($this->request_api)) {
            $this->request_api = new RequestAPI();
        }
        $response = $this->request_api->sendRoute($url, "");
        $response = json_decode($response);
        if (isset($response->code)) {
            return new JsonResponse(array("message" => $response->message), $response->code);
        }
        return new JsonResponse(array("response" => $response), 200);
    }

    /**
     * @Route("/twitter/connect", name="twitter_api_connect")
     */
    public function connect(Request $request, ServiceRepository $service_repository)
    {
        header('Access-Control-Allow-Origin: *');
        // Get needed values
        $request_content = json_decode($request->getContent());
        if (empty($request_content->redirect_uri)) {
            return new JsonResponse(array("message" => "Twitter: Missing field"), 400);
        }
        $redirect_uri = $request_content->redirect_uri;
        $service = $service_repository->findByName("twitter");
        if (empty($service)) {
            return new JsonResponse(array("message" => "Twitter: Service not found"), 404);
        }
        $service = $service[0];
        $identifiers = explode(";", $service->getIdentifiers());
        if (empty($identifiers)) {
            return new JsonResponse(array("message" => "Twitter: Identifiers error"), 422);
        }
        $client_id = $identifiers[0];
        // Compose the authorization scope
        $scope = array(
            "read:user"
        );
        $scope = implode(" ", $scope);
        // Set the state when the request is good
        $state = "17";
        // Set the code verifier useful for the code challenge
        $code_verifier = base64_encode(random_bytes(32));
        $code_verifier = strtr(rtrim($code_verifier, '='), '+/', '-_');
        // Set the code challenge for the autorization url
        $code_challenge = base64_encode(hash('sha256', $code_verifier, true));
        $code_challenge = strtr(rtrim($code_challenge, '='), '+/', '-_');
        // Compose the authorization url
        $authorization_url = "https://twitter.com/i/oauth2/authorize?client_id=$client_id&redirect_uri=$redirect_uri&response_type=code&scope=like.write%20tweet.write%20tweet.read%20users.read%20follows.read%20follows.write%20offline.access&state=$state&code_challenge=$code_challenge&code_challenge_method=plain";
        return new JsonResponse(array("authorization_url" => $authorization_url), 200);
    }
    /**
     * @Route("/twitter/get_access_token", name="twitter_api_get_access_token")
     */
    public function getAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
    {
        header('Access-Control-Allow-Origin: *');
        // Get needed values
        $request_content = json_decode($request->getContent());
        if (empty($request_content->state)) {
            return new JsonResponse(array("message" => "Twitter: Missing field"), 400);
        }
        $state = $request_content->state;
        if ($state != "17") {
            return new JsonResponse(array("message" => "Twitter: Bad request to get access token"), 400);
        }
        if (empty($request_content->token) || empty($request_content->redirect_uri) || empty($request_content->code)) {
            return new JsonResponse(array("message" => "Twitter: Missing field"), 400);
        }
        $token = $request_content->token;
        if (empty($user_repository->findByToken($token))) {
            return new JsonResponse(array("message" => "Twitter: Bad auth token"), 400);
        }
        $user = $user_repository->findByToken($token)[0];
        $user_id = $user->getId();
        $code = $request_content->code;
        $redirect_uri = $request_content->redirect_uri;
        $service = $service_repository->findByName("twitter");
        if (empty($service)) {
            return new JsonResponse(array("message" => "Twitter: Service not found"), 404);
        }
        $service = $service[0];
        $identifiers = explode(";", $service->getIdentifiers());
        if (count($identifiers) != 2) {
            return new JsonResponse(array("message" => "Twitter: Identifiers error"), 422);
        }
        $client_id = $identifiers[0];
        $client_secret = $identifiers[1];
        // Request for the access token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.twitter.com/oauth2/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=$code&redirect_uri=$redirect_uri");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$client_secret");
        $headers = array();
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);

        return new JsonResponse(json_decode($result), 400);

        if (!isset(json_decode($result)->access_token)) {
            return new JsonResponse(array("message" => "Twitter: Bad code to get access token"), 400);
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
        $user_service_repository->add($user_service, true);
        return new JsonResponse(array("message" => "OK", 200));
    }
    /**
     * @Route("/twitter/refresh_access_token", name="twitter_api_refresh_access_token")
     */
    public function refreshAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
    {
        // Get needed values
        $request_content = json_decode($request->getContent());
        if (empty($request_content->user_id)) {
            return new JsonResponse(array("message" => "Twitter: Missing field"), 400);
        }
        $user_id = $request_content->user_id;
        $service = $service_repository->findByName("twitter");
        if (empty($service)) {
            return new JsonResponse(array("message" => "Twitter: Service not found"), 404);
        }
        $service = $service[0];
        $identifiers = explode(";", $service->getIdentifiers());
        if (count($identifiers) != 2) {
            return new JsonResponse(array("message" => "Twitter: Identifiers error"), 422);
        }
        if (empty($user_service_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
            return new JsonResponse(array("message" => "Twitter: Refresh token not found"), 404);
        }
        $user_service = $user_service_repository->findByUserIdAndServiceId($user_id, $service->getId())[0];
        if (!empty($user_service)) {
            $user_service_repository->remove($user_service);
        }
        return new JsonResponse(array("message" => "OK"), 200);
    }

    /**
     * @Route("/twitter/connected", name="twitter_api_connected")
     */
    public function isConnected(Request $request, ServiceRepository $sevice_repository, UserRepository $user_repository, UserServiceRepository $user_sevice_repository)
    { // a changer pour lutiliser que via le server
        header('Access-Control-Allow-Origin: *');
        // Get needed values
        $request_content = json_decode($request->getContent());
        if (empty($request_content->token)) {
            return new JsonResponse(array("message" => "Twitter: Missing field"), 400);
        }
        $token = $request_content->token;
        if (empty($user_repository->findByToken($token))) {
            return new JsonResponse(array("message" => "Twitter: Bad auth token"), 400);
        }
        $user = $user_repository->findByToken($token)[0];
        $user_id = $user->getId();
        $service = $sevice_repository->findByName("twitter");
        if (empty($service)) {
            return new JsonResponse(array("message" => "Twitter: Service not found"), 404);
        }
        $service = $service[0];
        if (empty($user_sevice_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
            return new JsonResponse(array("connected" => false), 200);
        }
        return new JsonResponse(array("connected" => true), 200);
    }

    private function sendRequest($access_token, $endpoint, $method = "GET", $parameters = array())
    {
        if (empty($this->request_api)) {
            $this->request_api = new RequestAPI();
        }
        $response = $this->request_api->send($access_token, self::API_URL . $endpoint, $method, $parameters);
        if (isset(json_decode($response)->error)) {
            switch (json_decode($response)->error->status) {
                case 400:
                    $response = json_encode(array("message" => "Twitter: Bad request", "code" => 400));
                    break;
                case 401:
                    $response = json_encode(array("message" => "Twitter: Bad or expired token", "code" => 401));
                    break;
                case 403:
                    $response = json_encode(array("message" => "Twitter: Forbidden", "code" => 403));
                    break;
                case 404:
                    $response = json_encode(array("message" => "Twitter: Ressource not found", "code" => 404));
                    break;
                case 429:
                    $response = json_encode(array("message" => "Twitter: Too many requests", "code" => 429));
                    break;
                case 500:
                    $response = json_encode(array("message" => "Twitter: Internal server error", "code" => 500));
                    break;
                case 502:
                    $response = json_encode(array("message" => "Twitter: Bad gateway", "code" => 502));
                    break;
                case 503:
                    $response = json_encode(array("message" => "Twitter: Service unavailable", "code" => 503));
                    break;
                default:
                    break;
            }
        }
        return $response;
    }

    /**
     * @Route("/twitter/reaction/change_status", name="twitter_api_change_status")
     */

    function changeStatus($accessToken, $accessTokenSecret, $status = "Tweet de test !")
    {
        // Concaténation des jetons d'accès pour construire l'en-tête d'authentification
        $bearerToken = base64_encode($accessToken . ":" . $accessTokenSecret);
        $authorizationHeader = "Authorization: Bearer " . $bearerToken;

        // encode le contenu du tweet
        $status = urlencode($status);

        $ch = curl_init();
        // Configuration de l'URL de l'API Twitter avec l'endpoint pour la gestion des tweets
        curl_setopt($ch, CURLOPT_URL, "https://api.twitter.com/2/tweets");
        curl_setopt($ch, CURLOPT_POST, true);
        // données utilisé par la requếte
        curl_setopt($ch, CURLOPT_POSTFIELDS, "status=$status");
        // config du header
        curl_setopt($ch, CURLOPT_HTTPHEADER, [$authorizationHeader]);
        // Désactivation de la vérification SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Exécution de la requête
        $response = curl_exec($ch);
        curl_close($ch);

        // Analyse de la réponse de l'API pour déterminer si le tweet a été publié avec succès
        $responseObject = json_decode($response);
        if (isset($responseObject->id)) {
            return true;
        } else {
            return false;
        }
    }
}
