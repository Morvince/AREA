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
        * @brief This function is used to connect the service with the user account 
        * 
        * @param[in] request used to get the content of the old username and the new one
        * @param[in] service_repo to access linked values on database
        * @return JsonReponse will return a Json object containing all the information when the action is working
        */

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
        * @brief This function is used to get the access_token with the OAuth request
        * 
        * @param[in] request used to get the content of the old username and the new one
        * @param[in] automation_repo to access linked values on database
        * @param[in] automation_action_repo to access linked values on database
        * @param[in] service_repo to access linked values on database
        * @param[in] user_service_repo to access linked values on database
        * @return JsonReponse will return a Json object containing all the information when the action is working
        */

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
        * @brief This function is used to set the new refresh token for the user
        * 
        * @param[in] request used to get the content of the old username and the new one
        * @param[in] automation_repo to access linked values on database
        * @param[in] automation_action_repo to access linked values on database
        * @param[in] service_repo to access linked values on database
        * @param[in] user_service_repo to access linked values on database
        * @return JsonReponse will return a Json object containing all the information when the action is working
        */

        /**
         * @Route("/twitter/refresh_access_token", name="twitter_api_refresh_access_token")
         */
        public function refreshAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->access_token)) {
                return new JsonResponse(array("message" => "Twitter: Missing field"), 400);
            }
            $access_token = $request_content->access_token;
            $service = $service_repository->findByName("twitter");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Twitter: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 2) {
                return new JsonResponse(array("message" => "Twitter: Identifiers error"), 422);
            }
            if (empty($user_service_repository->findBy(array("access_token" => $access_token)))) {
                return new JsonResponse(array("message" => "Twitter: Refresh token not found"), 404);
            }
            $client_id = $identifiers[0];
            $client_secret = $identifiers[1];
            $code_challenge = "TUHk8FoWnFaNw2xMcM6Nm/MUOE+y+n0pMkksPyctkSA=";
            $user_service = $user_service_repository->findBy(array("access_token" => $access_token))[0];
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
                $user_service_repository->remove($user_service, true);
                return new JsonResponse(array("message" => "Twitter: Expired refresh token"), 400);
            }
            // Edit datas in database
            $user_service->setAccessToken(json_decode($result)->access_token);
            $user_service_repository->add($user_service, true);
            return new JsonResponse(array("message" => "OK"), 200);
        }

        /**
        * @brief This function is used to get if the service is connected and currently working
        * 
        * @param[in] request used to get the content of the old username and the new one
        * @param[in] automation_repo to access linked values on database
        * @param[in] automation_action_repo to access linked values on database
        * @param[in] service_repo to access linked values on database
        * @param[in] user_service_repo to access linked values on database
        * @return JsonReponse will return a Json object containing all the information when the action is working
        */

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

        /**
        * @brief This function allow to sendRequest to the API with the given parameters
        * 
        * @param[in] access_token when you need to request via an user account
        * @param[in] endpoint to use for your request
        * @param[in] method to precise the method used for the request
        * @param[in] parameters if you need to access on the database parameters for reaction/action
        * @param[in] added_header to modify the request header with others informations
        * @param[in] authorization to precise the different authorization for the request
        * @return JsonReponse will return a Json object containing all the information when working
         */

        private function sendRequest($access_token, $endpoint, $method = "GET", $parameters = array())
        {
            if (empty($this->request_api)) {
                $this->request_api = new RequestAPI();
            }
            $response = json_decode($this->request_api->send($access_token, self::API_URL . $endpoint, $method, $parameters));
            if (isset($response->errors) || isset($response->title) || isset($response->detail) || isset($response->status)) {
                $this->request_api->sendRoute("http://localhost/twitter/refresh_access_token", array("access_token" => $access_token));
                $response = array("message" => "Twitter: error for the endpoint ".self::API_URL.$endpoint, "code" => 400);
            }
            return json_decode(json_encode($response));
        }

        // Action

        /**
        * @brief This function is used to check if the pinned tweet change on user account
        * 
        * @param[in] request used to get the content of the old username and the new one
        * @param[in] automation_repo to access linked values on database
        * @param[in] automation_action_repo to access linked values on database
        * @param[in] service_repo to access linked values on database
        * @param[in] user_service_repo to access linked values on database
        * @return JsonReponse will return a Json object containing all the information when the action is working
        */
        /**
         * @Route("/twitter/action/check_tweet", name="twitter_api_check_tweet")
         */
        public function checkNewPinnedTweetOnUser(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->new) || empty($request_content->old)) {
                return new JsonResponse(array("message" => "Twitter: Missing field"), 400);
            }
            $old_pinned = $request_content->old;
            $new_pinned = $request_content->new;
            // Check if pinned tweet has changed
            if (strcmp($old_pinned, $new_pinned) === 0) {
                return new JsonResponse(array("message" => false), 200);
            }
            return new JsonResponse(array("message" => true), 200);
        }

        /**
        * @brief This function is used to check if the pinned tweet parameters to use the action
        * 
        * @param[in] request used to get the content of the old username and the new one
        * @param[in] automation_repo to access linked values on database
        * @param[in] automation_action_repo to access linked values on database
        * @param[in] service_repo to access linked values on database
        * @param[in] user_service_repo to access linked values on database
        * @return JsonReponse will return a Json object containing all the information when the action is working
        */

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
            // Request to get the pinned tweet of the user
            $pinned_tweet = $this->sendRequest($access_token, "users/me?expansions=pinned_tweet_id");
            if (isset($pinned_tweet->code)) {
                return new JsonResponse(array("message" => $pinned_tweet->message), $pinned_tweet->code);
            }
            return new JsonResponse($pinned_tweet->data->pinned_tweet_id, 200);
        }

        // Reaction

        /**
        * @brief This function is used to send a tweet with the request on the good endpoint for twitter
        * 
        * @param[in] request used to get the content of the old username and the new one
        * @param[in] automation_repo to access linked values on database
        * @param[in] automation_action_repo to access linked values on database
        * @param[in] service_repo to access linked values on database
        * @param[in] user_service_repo to access linked values on database
        * @return JsonReponse will return a Json object containing all the information when the action is working
        */

        /**
         * @Route("/twitter/reaction/send_tweet", name="twitter_api_send_tweet")
         */
        public function sendTweet(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Twitter: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Twitter: Automation_action ID not found"), 404);
            }
            $informations = $automation_action->getInformations();
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
            // Request to post the tweet
            $response = $this->sendRequest($access_token, "tweets", "POST", array("text" => $informations->tweet));
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }

        /**
        * @brief This function is used to like a tweet for a specific word given
        * 
        * @param[in] request used to get the content of the old username and the new one
        * @param[in] automation_repo to access linked values on database
        * @param[in] automation_action_repo to access linked values on database
        * @param[in] service_repo to access linked values on database
        * @param[in] user_service_repo to access linked values on database
        * @return JsonReponse will return a Json object containing all the information when the action is working
        */

        /**
         * @Route("/twitter/reaction/like_tweet", name="twitter_api_like_tweet")
         */
        public function likeRandomTweet(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            srand(time());
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Twitter: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Twitter: Automation_action ID not found"), 404);
            }
            $informations = $automation_action->getInformations();
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
            // Request to get the user id
            $response = $this->sendRequest($access_token, "users/me");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            $user_id = $response->data->id;
            // Request to get a random tweet with a search
            $response = $this->sendRequest($access_token, "tweets/search/recent?query=".urlencode($informations->search." lang:fr")."&max_results=25&tweet.fields=author_id,id");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            $tweets = $response->data;
            $random_tweet = $tweets[array_rand($tweets)];
            // Request to like the tweet
            $response = $this->sendRequest($access_token, "users/$user_id/likes", "POST", array("tweet_id" => $random_tweet->id));
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }
    }
?>
