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

    class GmailAPIController extends AbstractController
    {
        private const API_URL = "https://gmail.googleapis.com/gmail/v1/";
        private RequestAPI $request_api;

        /**
         * @brief This function is used to connect the service with the user account 
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when the action is working
         */

        /**
         * @Route("/gmail/connect", name="gmail_api_connect")
         */
        public function connect(Request $request, ServiceRepository $service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->redirect_uri)) {
                return new JsonResponse(array("message" => "Gmail: Missing field"), 400);
            }
            $redirect_uri = $request_content->redirect_uri;
            $service = $service_repository->findByName("gmail");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Gmail: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (empty($identifiers)) {
                return new JsonResponse(array("message" => "Gmail: Identifiers error"), 422);
            }
            $client_id = $identifiers[0];
            // Compose the authorization scope
            $scope = array(
                "https://www.googleapis.com/auth/gmail.compose", "https://www.googleapis.com/auth/gmail.insert",
                "https://www.googleapis.com/auth/gmail.labels", "https://www.googleapis.com/auth/gmail.metadata",
                "https://www.googleapis.com/auth/gmail.modify", "https://www.googleapis.com/auth/gmail.readonly",
                "https://www.googleapis.com/auth/gmail.send", "https://www.googleapis.com/auth/gmail.settings.basic",
                "https://www.googleapis.com/auth/gmail.settings.sharing"
            );
            $scope = implode(" ", $scope);
            // Set the state when the request is good
            $state = "17";
            // Compose the authorization url
            $authorization_url = "https://accounts.google.com/o/oauth2/auth?client_id=$client_id&response_type=code&redirect_uri=$redirect_uri&scope=$scope&state=$state&access_type=offline&prompt=consent";
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
         * @Route("/gmail/get_access_token", name="gmail_api_get_access_token")
         */
        public function getAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->state)) {
                return new JsonResponse(array("message" => "Gmail: Missing field"), 400);
            }
            $state = $request_content->state;
            if ($state != "17") {
                return new JsonResponse(array("message" => "Gmail: Bad request to get access token"), 400);
            }
            if (empty($request_content->token) || empty($request_content->redirect_uri) || empty($request_content->code)) {
                return new JsonResponse(array("message" => "Gmail: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Gmail: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user_id = $user->getId();
            $code = $request_content->code;
            $redirect_uri = $request_content->redirect_uri;
            $service = $service_repository->findByName("gmail");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Gmail: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 2) {
                return new JsonResponse(array("message" => "Gmail: Identifiers error"), 422);
            }
            $client_id = $identifiers[0];
            $client_secret = $identifiers[1];
            // Request for the access token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://oauth2.googleapis.com/token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=$code&redirect_uri=$redirect_uri");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$client_secret");
            $headers = array(
                            "Accept: application/json",
                            "Content-Type: application/x-www-form-urlencoded"
                        );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            curl_close($ch);
            if (!isset(json_decode($result)->access_token)) {
                return new JsonResponse(array("message" => "Gmail: Bad code to get access token"), 400);
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
         * @Route("/gmail/refresh_access_token", name="gmail_api_refresh_access_token")
         */
        public function refreshAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->access_token)) {
                return new JsonResponse(array("message" => "Gmail: Missing field"), 400);
            }
            $access_token = $request_content->access_token;
            $service = $service_repository->findByName("gmail");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Gmail: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 2) {
                return new JsonResponse(array("message" => "Gmail: Identifiers error"), 422);
            }
            if (empty($user_service_repository->findBy(array("access_token" => $access_token)))) {
                return new JsonResponse(array("message" => "Gmail: Refresh token not found"), 404);
            }
            $client_id = $identifiers[0];
            $client_secret = $identifiers[1];
            $user_service = $user_service_repository->findBy(array("access_token" => $access_token))[0];
            $refresh_token = $user_service->getRefreshToken();
            // Request for the access token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://oauth2.googleapis.com/token");
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
                return new JsonResponse(array("message" => "Gmail: Expired refresh token"), 400);
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
         * @Route("/gmail/connected", name="gmail_api_connected")
         */
        public function isConnected(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->token)) {
                return new JsonResponse(array("message" => "Gmail: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Gmail: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user_id = $user->getId();
            $service = $service_repository->findByName("gmail");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Gmail: Service not found"), 404);
            }
            $service = $service[0];
            if (empty($user_service_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                return new JsonResponse(array("connected" => false), 200);
            }
            return new JsonResponse(array("connected" => true), 200);
        }

        private function sendRequest($access_token, $endpoint, $method = "GET", $parameters = array())
        {
            if (empty($this->request_api)) {
                $this->request_api = new RequestAPI();
            }
            $response = json_decode($this->request_api->send($access_token, self::API_URL . $endpoint, $method, $parameters));
            if (isset($response->error)) {
                if ($response->error->code === 401) {
                    $response = json_decode($this->request_api->sendRoute("http://localhost/gmail/refresh_access_token", array("access_token" => $access_token)));
                    if (isset($response->code)) {
                        $response = array("message" => "Gmail: Refresh token error", "code" => $response->code);
                    } else {
                        return $this->sendRequest($access_token, $endpoint, $method, $parameters);
                    }
                } else {
                    $response = array("message" => "Gmail: ".$response->error->status, "code" => $response->error->code);
                }
            }
            return json_decode(json_encode($response));
        }

        // Action

        /**
         * @brief This function is used to check if there is new mail received
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @return JsonReponse will return a Json object containing all the information when working
         */

        /**
         * @Route("/gmail/action/check_mail", name="gmail_api_action_check_mail")
         */
        public function hasInboxNewMail(Request $request)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->new) || empty($request_content->old)) {
                return new JsonResponse(array("message" => "Gmail: Missing field"), 400);
            }
            $old_mails = $request_content->old;
            $new_mails = $request_content->new;
            // Check if a new mail is in the inbox
            foreach ($new_mails as $new_mail) {
                $found = false;
                foreach ($old_mails as $old_mail) {
                    if (strcmp($new_mail->id, $old_mail->id) === 0) {
                        $found = true;
                    }
                }
                if ($found === false) {
                    return new JsonResponse(array("message" => true), 200);
                }
            }
            return new JsonResponse(array("message" => false), 200);
        }

        /**
         * @brief This function is used to get the parameters for the action on mail received
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when working
         */

        /**
         * @Route("/gmail/action/check_mail/get_parameters", name="gmail_api_action_check_mail_parameters")
         */
        public function getHasInboxNewMailParameters(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Gmail: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Gmail: automation_action ID not found"), 404);
            }
            $service = $service_repository->findByName("gmail");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Gmail: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Gmail: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            // Request to get the 5 last mails
            $response = $this->sendRequest($access_token, "users/me/messages?maxResults=5");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse($response->messages, 200);
        }

        /**
         * @brief This function is used to check mail received from a special user
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when working
         */

        /**
         * @Route("/gmail/action/check_mail_from_somebody", name="gmail_api_action_check_mail_from_somebody")
         */
        public function hasInboxNewMailFromSomebody(Request $request, AutomationActionRepository $automation_action_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->new) || empty($request_content->old) || empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Gmail: Missing field"), 400);
            }
            if (empty($automation_action_repository->find($request_content->automation_action_id))) {
                return new JsonResponse(array("message" => "Gmail: Automation_action not found"), 404);
            }
            $automation_action = $automation_action_repository->find($request_content->automation_action_id);
            $informations = $automation_action->getInformations();
            $from = $informations->from;
            $old_mails = $request_content->old;
            $new_mails = $request_content->new;
            // Check if a new mail from somebody is in the inbox
            foreach ($new_mails as $new_mail) {
                $found = false;
                foreach ($old_mails as $old_mail) {
                    if (strcmp($new_mail->id, $old_mail->id) !== 0) {
                        foreach ($new_mail->payload->headers as $header) {
                            if (strcmp($header->name, "From") === 0 && strcmp($header->value, $from) === 0) {
                                $found = true;
                            }
                        }
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
         * @brief This function is used to get the parameters of the check specific mail received
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when working
         */

        /**
         * @Route("/gmail/action/check_mail_from_somebody/get_parameters", name="gmail_api_action_check_mail_from_somebody_parameters")
         */
        public function getHasInboxNewMailFromSomebodyParameters(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Gmail: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Gmail: automation_action ID not found"), 404);
            }
            $service = $service_repository->findByName("gmail");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Gmail: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Gmail: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            // Request to get the 5 last mails
            $response = $this->sendRequest($access_token, "users/me/messages?maxResults=5");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse($this->sendRequest($access_token, "users/me/messages/".$response->messages[0]->id));
            // return new JsonResponse($response->messages, 200);
        }

        // Reaction

        /**
         * @brief This function is used to send a mail thanks to an API request
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when working
         */

        /**
         * @Route("/gmail/reaction/send_mail", name="gmail_api_reaction_send_mail")
         */
        public function sendMail(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Gmail: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Gmail: automation_action ID not found"), 404);
            }
            $service = $service_repository->findByName("gmail");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Gmail: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Gmail: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            $informations = $automation_action->getInformations();
            if (empty($informations->to)) {
                return new JsonResponse(array("message" => "Gmail: Email not found"), 404);
            }
            $to = $informations->to;
            if (empty($informations->subject)) {
                return new JsonResponse(array("message" => "Gmail: Suject not found"), 404);
            }
            $subject = $informations->subject;
            if (empty($informations->body)) {
                return new JsonResponse(array("message" => "Gmail: Body not found"), 404);
            }
            $body = $informations->body;
            $parameters = array(
                "raw" => base64_encode("To: $to\r\nSubject: $subject\r\n\r\n$body")
            );
            // Request to change send the mail
            $response = $this->sendRequest($access_token, "users/me/messages/send", "POST", $parameters);
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }
    }
?>