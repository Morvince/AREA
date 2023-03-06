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
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;

    class TwitchAPIController extends AbstractController
    {
        private const API_URL = "https://api.twitch.tv/";
        private RequestAPI $request_api;

        /**
         * @brief This function is used to connect the service with the user account 
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when the action is working
         */

        /**
         * @Route("/twitch/connect", name="twitch_api_connect")
         */
        public function connect(Request $request, ServiceRepository $service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->redirect_uri)) {
                return new JsonResponse(array("message" => "Twitch: Missing field"), 400);
            }
            $redirect_uri = $request_content->redirect_uri;
            $service = $service_repository->findByName("twitch");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Twitch: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (empty($identifiers)) {
                return new JsonResponse(array("message" => "Twitch: Identifiers error"), 422);
            }
            $client_id = $identifiers[0];
            // Compose the authorization scope
            $scope = array(
                "analytics:read:extensions", "analytics:read:games", "bits:read",
                "channel:edit:commercial", "channel:manage:broadcast", "channel:read:charity",
                "channel:manage:extensions", "channel:manage:moderators", "channel:manage:polls",
                "channel:manage:predictions", "channel:manage:raids", "channel:manage:redemptions",
                "channel:manage:schedule", "channel:manage:videos", "channel:read:editors",
                "channel:read:goals", "channel:read:hype_train", "channel:read:polls",
                "channel:read:predictions", "channel:read:redemptions", "channel:read:stream_key",
                "channel:read:subscriptions", "channel:read:vips", "channel:manage:vips",
                "clips:edit", "moderation:read", "moderator:manage:announcements", "moderator:manage:automod",
                "moderator:read:automod_settings", "moderator:manage:automod_settings", "moderator:manage:banned_users",
                "moderator:read:blocked_terms", "moderator:manage:blocked_terms", "moderator:manage:chat_messages",
                "moderator:read:chat_settings", "moderator:manage:chat_settings", "moderator:read:chatters",
                "moderator:read:followers", "moderator:read:shield_mode", "moderator:manage:shield_mode",
                "moderator:read:shoutouts", "user:edit", "user:manage:blocked_users", "user:read:blocked_users",
                "user:read:broadcast", "user:manage:chat_color", "user:read:email", "user:read:follows",
                "user:read:subscriptions", "user:manage:whispers", "chat:edit", "channel:moderate",
                "whispers:read", "whispers:edit"
            );
            $scope = implode(" ", $scope);
            // Set the state when the request is good
            $state = "17";
            // Compose the authorization url
            $authorization_url = "https://id.twitch.tv/oauth2/authorize?client_id=$client_id&response_type=code&redirect_uri=$redirect_uri&scope=$scope&state=$state";
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
         * @Route("/twitch/get_access_token", name="twitch_api_get_access_token")
         */
        public function getAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->state)) {
                return new JsonResponse(array("message" => "Twitch: Missing field"), 400);
            }
            $state = $request_content->state;
            if ($state != "17") {
                return new JsonResponse(array("message" => "Twitch: Bad request to get access token"), 400);
            }
            if (empty($request_content->token) || empty($request_content->redirect_uri) || empty($request_content->code)) {
                return new JsonResponse(array("message" => "Twitch: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Twitch: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user_id = $user->getId();
            $code = $request_content->code;
            $redirect_uri = $request_content->redirect_uri;
            $service = $service_repository->findByName("twitch");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Twitch: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 2) {
                return new JsonResponse(array("message" => "Twitch: Identifiers error"), 422);
            }
            $client_id = $identifiers[0];
            $client_secret = $identifiers[1];
            // Request for the access token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://id.twitch.tv/oauth2/token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "client_id=$client_id&client_secret=$client_secret&grant_type=authorization_code&code=$code&redirect_uri=$redirect_uri");
            curl_setopt($ch, CURLOPT_POST, true);
            $headers = array();
            $headers[] = "Content-Type: application/x-www-form-urlencoded";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            curl_close($ch);
            if (!isset(json_decode($result)->access_token)) {
                return new JsonResponse(array("message" => "Twitch: Bad code to get access token"), 400);
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
         *  @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when the action is working
         */

        /**
         * @Route("/twitch/refresh_access_token", name="twitch_api_refresh_access_token")
         */
        public function refreshAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->access_token)) {
                return new JsonResponse(array("message" => "Twitch: Missing field"), 400);
            }
            $access_token = $request_content->access_token;
            $service = $service_repository->findByName("twitch");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Twitch: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 2) {
                return new JsonResponse(array("message" => "Twitch: Identifiers error"), 422);
            }
            if (empty($user_service_repository->findBy(array("access_token" => $access_token)))) {
                return new JsonResponse(array("message" => "Twitch: Refresh token not found"), 404);
            }
            $client_id = $identifiers[0];
            $client_secret = $identifiers[1];
            $user_service = $user_service_repository->findBy(array("access_token" => $access_token))[0];
            $refresh_token = $user_service->getRefreshToken();
            // Request for the access token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://id.twitch.tv/oauth2/token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=refresh_token&refresh_token=$refresh_token&client_id=$client_id&client_secret=$client_secret");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$client_secret");
            $headers = array();
            $headers[] = "Content-Type: application/x-www-form-urlencoded";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            curl_close($ch);
            if (empty(json_decode($result)->access_token)) {
                $user_service_repository->remove($user_service, true);
                return new JsonResponse(array("message" => "Twitch: Expired refresh token"), 400);
            }
            // Edit datas in database
            $user_service->setAccessToken(json_decode($result)->access_token);
            $user_service->setRefreshToken(json_decode($result)->refresh_token);
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
         * @Route("/twitch/connected", name="twitch_api_connected")
         */
        public function isConnected(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->token)) {
                return new JsonResponse(array("message" => "Twitch: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Twitch: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user_id = $user->getId();
            $service = $service_repository->findByName("twitch");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Twitch: Service not found"), 404);
            }
            $service = $service[0];
            if (empty($user_service_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                return new JsonResponse(array("connected" => false), 200);
            }
            return new JsonResponse(array("connected" => true), 200);
        }

        private function sendRequest(ServiceRepository $service_repository, $access_token, $endpoint, $method = "GET", $parameters = array())
        {
            if (empty($this->request_api)) {
                $this->request_api = new RequestAPI();
            }
            $service = $service_repository->findByName("twitch");
            if (empty($service)) {
                return json_decode(json_encode(array("message" => "Twitch: Service not found", "code" => 404)));
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 2) {
                return json_decode(json_encode(array("message" => "Twitch: Identifiers error", "code" => 422)));
            }
            $client_id = $identifiers[0];
            $response = json_decode($this->request_api->send($access_token, self::API_URL . $endpoint, $method, $parameters, "Client-ID: $client_id"));
            if (isset($response->error)) {
                if ($response->status === 401) {
                    $response = json_decode($this->request_api->sendRoute("http://localhost/twitch/refresh_access_token", array("access_token" => $access_token)));
                    if (isset($response->code)) {
                        $response = array("message" => "Twitch: Refresh token error", "code" => $response->code);
                    } else {
                        return $this->sendRequest($service_repository, $access_token, $endpoint, $method, $parameters);
                    }
                } else {
                    $response = array("message" => "Twitch: $response->error $response->message", "code" => $response->status);
                }
            }
            return json_decode(json_encode($response));
        }

        // Action

        /**
         * @brief This function is used to check if there is a new follow for the user account
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @return JsonReponse will return a Json object containing all the information when the action is working
         */

        /**
         * @Route("/twitch/action/check_follower", name="twitch_api_action_check_follower")
         */
        public function isNewUserFollowed(Request $request)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->new) || empty($request_content->old)) {
                return new JsonResponse(array("message" => "Twitch: Missing field"), 400);
            }
            $old_followers = $request_content->old;
            $new_followers = $request_content->new;
            // Check if a new user is following
            foreach ($new_followers as $new_follower) {
                $found = false;
                foreach ($old_followers as $old_follower) {
                    if (strcmp($new_follower->user_id, $old_follower->user_id) === 0) {
                        $found = true;
                        break (1);
                    }
                }
                if ($found === false) {
                    return new JsonResponse(array("message" => true), 200);
                }
            }
            return new JsonResponse(array("message" => false), 200);
        }

        /**
         * @brief This function is used to get the parameters of the account for the user following list
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when the action is working
         */

        /**
         * @Route("/twitch/action/check_follower/get_parameters", name="twitch_api_action_check_follower_parameters")
         */
        public function getIsNewUserFollowedParameters(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Twitch: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Twitch: automation_action ID not found"), 404);
            }
            $service = $service_repository->findByName("twitch");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Twitch: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Twitch: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            // Request to get the user
            $response = $this->sendRequest($service_repository, $access_token, "helix/users");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            $user_id = $response->data[0]->id;
            // Request to get the 20 last followers
            $response = $this->sendRequest($service_repository, $access_token, "helix/channels/followers?broadcaster_id=$user_id&first=20");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse($response->data, 200);
        }

        // Reaction

        /**
         * @brief This function is used to clean a stream chat by erasing all chat message
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when working
         */

        /**
         * @Route("/twitch/reaction/clean_stream_chat", name="twitch_api_reaction_clean_stream_chat")
         */
        public function cleanStreamChat(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Twitch: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Twitch: automation_action ID not found"), 404);
            }
            $service = $service_repository->findByName("twitch");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Twitch: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Twitch: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            // Request to get the user
            $response = $this->sendRequest($service_repository, $access_token, "helix/users");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            $user_id = $response->data[0]->id;
            // Request to delete all chat messages
            $response = $this->sendRequest($service_repository, $access_token, "helix/moderation/chat?broadcaster_id=$user_id&moderator_id=$user_id", "DELETE");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }
    }
?>