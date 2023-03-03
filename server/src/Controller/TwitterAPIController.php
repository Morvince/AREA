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
            // Set the code verifier useful for the code challenge
            $code_challenge = "TUHk8FoWnFaNw2xMcM6Nm/MUOE+y+n0pMkksPyctkSA=";
            // Set the state when the request is good
            $state = "17";
            // Compose the authorization url
            $authorization_url = "https://twitter.com/i/oauth2/authorize?client_id=$client_id&redirect_uri=$redirect_uri&response_type=code&scope=like.read%20like.write%20tweet.write%20tweet.read%20users.read%20follows.read%20follows.write%20offline.access&state=$state&code_challenge=$code_challenge&code_challenge_method=plain";
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
            $code_challenge = "TUHk8FoWnFaNw2xMcM6Nm/MUOE+y+n0pMkksPyctkSA=";
            // Request for the access token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.twitter.com/2/oauth2/token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=$code&redirect_uri=$redirect_uri&code_verifier=$code_challenge");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$client_secret");
            $headers = array();
            $headers[] = "Content-Type: application/x-www-form-urlencoded";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            curl_close($ch);
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
            $user_service->setRefreshToken(json_decode($result)->refresh_token);
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
            if (count($identifiers) != 3) {
                return new JsonResponse(array("message" => "Twitter: Identifiers error"), 422);
            }
            if (empty($user_service_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                return new JsonResponse(array("message" => "Twitter: Refresh token not found"), 404);
            }
            $client_id = $identifiers[0];
            $client_secret = $identifiers[1];
            $code_challenge = "TUHk8FoWnFaNw2xMcM6Nm/MUOE+y+n0pMkksPyctkSA=";
            $user_service = $user_service_repository->findByUserIdAndServiceId($user_id, $service->getId())[0];
            $refresh_token = $user_service->getRefreshToken();
            // Request for the access token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.twitter.com/2/oauth2/token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=refresh_token&refresh_token=$refresh_token&code_verifier=$code_challenge");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$client_secret");
            $headers = array();
            $headers[] = "Content-Type: application/x-www-form-urlencoded";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            curl_close($ch);
            if (!isset(json_decode($result)->access_token)) {
                $user_service_repository->remove($user_service);
                return new JsonResponse(array("message" => "Twitter: Expired refresh token"), 400);
            }
            // Edit datas in database
            $user_service->setAccessToken(json_decode($result)->access_token);
            $user_service_repository->add($user_service, true);
            return new JsonResponse(array("message" => "OK"), 200);
        }

        /**
         * @Route("/twitter/connected", name="twitter_api_connected")
         */
        public function isConnected(Request $request, ServiceRepository $sevice_repository, UserRepository $user_repository, UserServiceRepository $user_sevice_repository)
        {
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
        {// CHANGE
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

        // ACTION
        /**
         * @Route("/twitter/action/check_tweet", name="twitter_api_check_tweet")
         */
        public function checkNewPinnedTweetOnUser(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            $request_content = json_decode($request->getContent());
            if (empty($request_content->new) || empty($request_content->old)) {
                return new JsonResponse(array("message" => "Twitter: Missing field"), 400);
            }
            $old_pinned = $request_content->old;
            $new_pinned = $request_content->new;
            // Check if pinned have been added to playlist
            if (strcmp($old_pinned, $new_pinned) === 0) {
                return new JsonResponse(array("message" => false), 200);
            }
            return new JsonResponse(array("message" => true), 200);
        }
        /**
         * @Route("/twitter/action/check_tweet/get_parameters", name="twitter_api_action_check_tweet_parameters")
         */
        public function getIsNewTweetOnUserParameters(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Twitter: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Twitter: automation_action ID not found"), 404);
            }
            $service = $service_repository->findByName("twitter");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Twitter: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Twitter: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            // Request to get the pinned tweet from a user
            $url = "https://api.twitter.com/2/users/me?expansions=pinned_tweet_id";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $access_token));
            $response = curl_exec($ch);
            $user = json_decode($response);
            $pinned_tweet = $user->data->pinned_tweet_id;
            curl_close($ch);
            if (isset($pinned_tweet->code)) {
                return new JsonResponse(array("message" => $pinned_tweet->message), $pinned_tweet->code);
            }
            return new JsonResponse($pinned_tweet, 200);
        }

        // REACTION
        /**
         * @Route("/twitter/reaction/send_tweet", name="twitter_api_send_tweet")
         */
        public function sendTweet(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Récupérez le jeton d'accès
            $request_content = json_decode($request->getContent()); // début de la récup du field informations link au fiels qui est link au front
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Twitter: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Twitter: Automation_action ID not found"), 404);
            }
            $informations = $automation_action->getInformations(); // attribution des valeurs d'informations dans la var info
            if (empty($informations->tweet)) {
                return new JsonResponse(array("message" => "Twitter: Informations not found"), 404);
            }
            $service = $service_repository->findByName("twitter");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Twitter: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Twitter: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            if (empty($this->request_api)) {
                $this->request_api = new RequestAPI();
            }
            // Set the API endpoint
            $url = "https://api.twitter.com/2/tweets";
            // Set the request headers
            $headers = array(
                "Authorization: Bearer $access_token",
                "Content-Type: application/json"
            );
            $tweet = $informations->tweet;
            // Set the request body
            $body = array(
                "text" => $tweet
            );
            $json_body = json_encode($body);
            // Send the request using cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            curl_close($ch);
            // Parse the response JSON
            $response = json_decode($result, true);
            return new JsonResponse($response, 200);
        }

        /**
         * @Route("/twitter/reaction/like_tweet", name="twitter_api_like_tweet")
         */
        public function likeRandomTweet(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Récupérez le jeton d'accès
            $request_content = json_decode($request->getContent()); // début de la récup du field informations link au fiels qui est link au front
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Twitter: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Twitter: Automation_action ID not found"), 404);
            }
            $informations = $automation_action->getInformations(); // attribution des valeurs d'informations dans la var info
            if (empty($informations->search)) {
                return new JsonResponse(array("message" => "Twitter: Informations not found"), 404);
            }
            $service = $service_repository->findByName("twitter");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Twitter: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Twitter: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            if (empty($this->request_api)) {
                $this->request_api = new RequestAPI();
            }
            // récupération de l'id user courant
            $url = "https://api.twitter.com/2/users/me";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $access_token));
            $response = curl_exec($ch);
            $user = json_decode($response);
            $user_id = $user->data->id;
            curl_close($ch);
            // Set the API endpoint
            $search_query = $informations->search;
            $url = "https://api.twitter.com/2/tweets/search/recent?query=" . urlencode($search_query . " lang:fr") . "&max_results=25&tweet.fields=author_id,id";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $access_token));
            $response = curl_exec($ch);
            curl_close($ch);
            // Analyse de la réponse
            $tweets = json_decode($response)->data;
            // Choix d'un tweet au hasard
            $random_tweet = $tweets[array_rand($tweets)];
            // Ajout d'un like au tweet choisi
            $like_url = "https://api.twitter.com/2/users/$user_id/likes"; // change l'id de l'utilisateur en dur par celui de l'utilisateur courant
            $like_data = array(
                "tweet_id" => $random_tweet->id
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $like_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer " . $access_token,
                "Content-Type: application/json"
            ));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($like_data));
            $response = curl_exec($ch);
            curl_close($ch);
            return new JsonResponse($response, 200);
        }
    }
?>
