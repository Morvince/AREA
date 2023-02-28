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
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\HttpFoundation\Request;

    class DiscordAPIController extends AbstractController
    {
        private const API_URL = "https://discordapp.com/api/v6/";
        private RequestAPI $request_api;

        /**
         * @Route("/discord/connect", name="discord_api_connect")
         */
        public function connect(Request $request, ServiceRepository $service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->redirect_uri)) {
                return new JsonResponse(array("message" => "Discord: Missing field"), 400);
            }
            $redirect_uri = $request_content->redirect_uri;
            $service = $service_repository->findByName("discord");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Discord: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (empty($identifiers)) {
                return new JsonResponse(array("message" => "Discord: Identifiers error"), 422);
            }
            $client_id = $identifiers[0];
            // Compose the authorization scope
            $scope = array(
                "email", "guilds",
                "connections", "messages.read",
                "identify", "gdm.join", "bot", "guilds.join"
            );
            $scope = implode(" ", $scope);
            // Set the state when the request is good
            $state = "17";
            // Compose the authorization url
            $authorization_url = "https://discord.com/api/oauth2/authorize?client_id=$client_id&response_type=code&redirect_uri=$redirect_uri&scope=$scope&permissions=8&state=$state";
            return new JsonResponse(array("authorization_url" => $authorization_url), 200);
        }
        /**
         * @Route("/discord/get_access_token", name="discord_api_get_access_token")
         */
        public function getAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->state)) {
                return new JsonResponse(array("message" => "Discord: Missing field"), 400);
            }
            $state = $request_content->state;
            if ($state != "17") {
                return new JsonResponse(array("message" => "Discord: Bad request to get access token"), 400);
            }
            if (empty($request_content->token) || empty($request_content->redirect_uri) || empty($request_content->code)) {
                return new JsonResponse(array("message" => "Discord: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Discord: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user_id = $user->getId();
            $code = $request_content->code;
            $redirect_uri = $request_content->redirect_uri;
            $service = $service_repository->findByName("discord");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Discord: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 3) {
                return new JsonResponse(array("message" => "Discord: Identifiers error"), 422);
            }
            $client_id = $identifiers[0];
            $client_secret = $identifiers[1];
            // Request for the access token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://discord.com/api/oauth2/token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=$code&redirect_uri=$redirect_uri");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$client_secret");
            $headers = array();
            $headers[] = "Content-Type: application/x-www-form-urlencoded";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            curl_close($ch);
            if (!isset(json_decode($result)->access_token)) {
                return new JsonResponse(array("message" => "Discord: Bad code to get access token"), 400);
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
         * @Route("/discord/refresh_access_token", name="discord_api_refresh_access_token")
         */
        public function refreshAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->user_id)) {
                return new JsonResponse(array("message" => "Discord: Missing field"), 400);
            }
            $user_id = $request_content->user_id;
            $service = $service_repository->findByName("discord");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Discord: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 3) {
                return new JsonResponse(array("message" => "Discord: Identifiers error"), 422);
            }
            if (empty($user_service_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                return new JsonResponse(array("message" => "Discord: Refresh token not found"), 404);
            }
            $client_id = $identifiers[0];
            $client_secret = $identifiers[1];
            $user_service = $user_service_repository->findByUserIdAndServiceId($user_id, $service->getId())[0];
            $refresh_token = $user_service->getRefreshToken();
            // Request for the access token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://discord.com/api/oauth2/token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=refresh_token&refresh_token=$refresh_token&client_id=$client_id&client_secret=$client_secret");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$client_secret");
            $headers = array();
            $headers[] = "Content-Type: application/x-www-form-urlencoded";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            curl_close($ch);
            if (!isset(json_decode($result)->access_token)) {
                $user_service_repository->remove($user_service);
                return new JsonResponse(array("message" => "Discord: Expired refresh token"), 400);
            }
            // Edit datas in database
            $user_service->setAccessToken(json_decode($result)->access_token);
            $user_service_repository->add($user_service, true);
            return new JsonResponse(array("message" => "OK"), 200);
        }
        /**
         * @Route("/discord/connected", name="discord_api_connected")
         */
        public function isConnected(Request $request, ServiceRepository $sevice_repository, UserRepository $user_repository, UserServiceRepository $user_sevice_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->token)) {
                return new JsonResponse(array("message" => "Discord: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Discord: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user_id = $user->getId();
            $service = $sevice_repository->findByName("discord");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Discord: Service not found"), 404);
            }
            $service = $service[0];
            if (empty($user_sevice_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                return new JsonResponse(array("connected" => false), 200);
            }
            return new JsonResponse(array("connected" => true), 200);
        }
        private function sendRequest($access_token, $endpoint, $method = "GET", $parameters = array(), $added_header = null, $authorization = "")
        {
            if (empty($this->request_api)) {
                $this->request_api = new RequestAPI();
            }
            $response = json_decode($this->request_api->send($access_token, self::API_URL . $endpoint, $method, $parameters, $added_header, $authorization));
            if (isset($response->code)) {
                $response = array("message" => "Discord: $response->message with code $response->code", "code" => 400);
            }
            return $response;
        }
        /**
         * @Route("/discord/get_user_channels", name="discord_api_get_user_channels")
         */
        public function getUserChannels(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->token)) {
                return new JsonResponse(array("message" => "Discord: Missing field"), 400);
            }
            $service = $service_repository->findByName("discord");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Discord: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 3) {
                return new JsonResponse(array("message" => "Discord: Identifiers error"), 422);
            }
            $bot_token = $identifiers[2];
            $guilds_url = 'https://discord.com/api/users/@me/guilds';
            $headers = [
                "Authorization: Bot $bot_token",
                "Content-Type: application/json"
            ];
            $options = [
                "http" => [
                    "header" => $headers,
                    "method" => "GET"
                ],
            ];
            // Request to get guilds
            $guilds_context = stream_context_create($options);
            $guilds_response = file_get_contents($guilds_url, false, $guilds_context);
            $guilds_data = json_decode($guilds_response, true);
            $guild_id = $guilds_data[0]['id'];
            $channels_url = "https://discord.com/api/guilds/$guild_id/channels";
            // Request to get channels
            $channels_context = stream_context_create($options);
            $channels_response = file_get_contents($channels_url, false, $channels_context);
            $channels_data = json_decode($channels_response, true);
            $allowed_channels = array();
            $bot_id = "1070728516188000336";
            // Check channel permissions
            foreach ($channels_data as $channel) {
                $permissions_url = "https://discord.com/api/channels/{$channel['id']}/permissions/{$bot_id}";
                $permissions_context = stream_context_create($options);
                $permissions_response = file_get_contents($permissions_url, false, $permissions_context);
                $permissions_data = json_decode($permissions_response, true);
                if ($permissions_data["allow"] & 1024) {
                    $allowed_channels[] = $channel["id"];
                }
            }
            // formatter array dans items pour avoir juste name (GuildName - ChannelName) et id dedans
            return new JsonResponse(array("items" => $allowed_channels), 200);
        }
        /**
         * @Route("/discord/get_thread_type", name="discord_api_get_thread_type")
         */
        public function getThreadType()
        {
            return new JsonResponse(array("items" => array("name" => "public", "id" => "public"), array("name" => "private", "id" => "private"), array("name" => "news", "id" => "news")), 200);
        }

        // Action
        /**
         * @Route("/discord/action/check_username", name="discord_api_check_username")
         */
        public function isUsernameChanged(Request $request)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->new) || empty($request_content->old)) {
                return new JsonResponse(array("message" => "Discord: Missing field"), 400);
            }
            $old_username = $request_content->old;
            $new_username = $request_content->new;
            // Check if username have been changed
            if (strcmp($new_username, $old_username) === 0) {
                return new JsonResponse(array("message" => false), 200);
            }
            return new JsonResponse(array("message" => true), 200);
        }
        /**
         * @Route("/discord/action/check_username/get_parameters", name="discord_api_check_username_parameters")
         */
        public function getIsUsernameChangedParameters(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Discord: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Discord: automation_action_id not found"), 404);
            }
            $service = $service_repository->findByName("discord");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Discord: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Discord: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            // Request to get the user
            $username = $this->sendRequest($access_token, "users/@me");
            if (isset($username->code)) {
                return new JsonResponse(array("message" => $username->message), $username->code);
            }
            return new JsonResponse($username->username, 200);
        }

        // Reaction
        /**
         * @Route("/discord/reaction/send_channel_message", name="discord_api_send_channel_message")
         */
        public function sendChannelMessage(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Discord: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Discord: Automation_action ID not found"), 404);
            }
            $informations = $automation_action->getInformations();
            if (empty($informations->channel_id) || empty($informations->message)) {
                return new JsonResponse(array("message" => "Discord: Informations not found"), 404);
            }
            $service = $service_repository->findByName("discord");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Discord: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Discord: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            if (empty($this->request_api)) {
                $this->request_api = new RequestAPI();
            }
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 3) {
                return new JsonResponse(array("message" => "Discord: Identifiers error"), 422);
            }
            $bot_token = $identifiers[2];
            // Request to get the user
            $response = $this->sendRequest($access_token, "users/@me");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            $username = $response->id;
            $message = $informations->message;
            $tag_message = "<@$username>";
            $message = $tag_message . $message;
            // $curl = curl_init();
            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => "https://discordapp.com/api/v6/channels/$informations->channel_id/messages",
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => "",
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => "POST",
            //     CURLOPT_POSTFIELDS => json_encode(array("content" => $message)),
            //     CURLOPT_HTTPHEADER => array(
            //         "Authorization: Bot $bot_token",
            //         "Content-Type: application/json"
            //     ),
            // ));
            // $response = curl_exec($curl);
            // curl_close($curl);

            // Request to send a message on a channel
            $response = $this->sendRequest("", "channels/$informations->channel_id/messages", "POST", array("content" => $message), null, "Authorization: Bot $bot_token");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }
        /**
         * @Route("/discord/reaction/send_private", name="discord_api_send_private")
         */
        public function sendPrivateMessage(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $service = $service_repository->findByName("discord");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Discord: Service not found"), 404);
            }
            $service = $service[0];
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Discord: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Discord: Automation_action ID not found"), 404);
            }
            $informations = $automation_action->getInformations();
            if (empty($informations->user_id) || empty($informations->message)) {
                return new JsonResponse(array("message" => "Discord: Informations not found"), 404);
            }
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 3) {
                return new JsonResponse(array("message" => "Discord: Identifiers error"), 422);
            }
            $bot_token = $identifiers[2];
            // $headers = array(
            //     "Authorization: Bot $bot_token",
            //     "Content-Type: application/json"
            // );
            $data = array("recipient_id" => $informations->user_id);
            // $ch = curl_init("https://discord.com/api/v9/users/@me/channels");
            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            // $response = curl_exec($ch);
            // curl_close($ch);
            // $channelData = json_decode($response, true);

            // Request to create the conversation
            $response = $this->sendRequest("", "users/@me/channels", "POST", $data, null, "Authorization: Bot $bot_token");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            $channel_id = $response->id;
            // $channelId = $channelData["id"];
            $data = array("content" => $informations->message);
            // $ch = curl_init("https://discord.com/api/v9/channels/$channel_id/messages");
            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            // $response = curl_exec($ch);
            // curl_close($ch);

            // Request to send the private message
            $response = $this->sendRequest("", "channels/$channel_id/messages", "POST", $data, null, "Authorization: Bot $bot_token");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }
        /**
         * @Route("/discord/reaction/react_message", name="discord_api_react_message")
         */
        public function reactWithMessage(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            srand(time());
            // Get needed values
            $service = $service_repository->findByName("discord");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Discord: Service not found"), 404);
            }
            $service = $service[0];
            $request_content = json_decode($request->getContent()); // début de la récup du field informations link au fiels qui est link au front
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Discord: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Discord: Automation_action ID not found"), 404);
            }
            $informations = $automation_action->getInformations();
            if (empty($informations->channel_id)) {
                return new JsonResponse(array("message" => "Discord: Informations not found"), 404);
            }
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 3) {
                return new JsonResponse(array("message" => "Discord: Identifiers error"), 422);
            }
            $bot_token = $identifiers[2];
            // $url = "https://discord.com/api/v9/channels/$informations->channel_id/messages?limit=1";
            // $headers = array(
            //     "Authorization: Bot $bot_token",
            //     "Content-Type: application/json",
            //     "Content-length: 0"
            // );
            // $curl = curl_init();
            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => $url,
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => "",
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => "GET",
            //     CURLOPT_HTTPHEADER => $headers,
            // ));
            // $response = curl_exec($curl);
            // curl_close($curl);

            // Request to get the last message
            $response = $this->sendRequest("", "channels/$informations->channel_id/messages?limit=1", "GET", array(), "Content-length: 0", "Authorization: Bot $bot_token");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            if (count($response) === 0) {
                return new JsonResponse(array("message" => "Discord: No message found in the channel"), 404);
            }
            $last_message_id = $response[0]->id;
            $reactions = array(
                "%F0%9F%98%83",
                "%F0%9F%98%84",
                "%F0%9F%98%86",
                "%F0%9F%98%8A",
                "%F0%9F%98%8D",
                "%F0%9F%98%8E",
                "%F0%9F%98%98",
                "%F0%9F%98%9C",
                "%F0%9F%98%9D",
                "%F0%9F%98%9E",
                "%F0%9F%98%A0",
                "%F0%9F%98%A2",
                "%F0%9F%98%A4",
                "%F0%9F%98%A9",
                "%F0%9F%98%AA",
                "%F0%9F%98%AB",
                "%F0%9F%98%AD",
                "%F0%9F%98%B0",
                "%F0%9F%98%B1",
                "%F0%9F%98%B2",
                "%F0%9F%98%B3",
                "%F0%9F%98%B4",
                "%F0%9F%98%B5",
                "%F0%9F%99%81",
                "%F0%9F%99%82",
                "%F0%9F%99%83",
                "%F0%9F%99%84",
                "%F0%9F%A4%94",
                "%F0%9F%A4%97",
                "%F0%9F%A4%94",
                "%F0%9F%A4%A9",
                "%F0%9F%A4%AA",
                "%F0%9F%A4%AC",
                "%F0%9F%A4%AF",
                "%F0%9F%A7%90",
                "%F0%9F%A4%AB",
                "%F0%9F%A4%94",
            );
            $index = array_rand($reactions);
            $reaction = $reactions[$index];
            // $url = "https://discord.com/api/v9/channels/$informations->channel_id/messages/$last_message_id/reactions/$reaction/@me";
            // $header = array(
            //     "Authorization: Bot $bot_token",
            //     "Content-Type: application/json",
            //     "Content-length: 12"
            // );
            // $curl = curl_init();
            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => $url,
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => "",
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => "PUT",
            //     CURLOPT_HTTPHEADER => $header,
            //     CURLOPT_POSTFIELDS => json_encode(array("Content" => "%F0%9F%91%8D")),
            // ));
            // $response = curl_exec($curl);
            // curl_close($curl);

            // Request to react to the message
            $response = $this->sendRequest("", "channels/$informations->channel_id/messages/$last_message_id/reactions/$reaction/@me", "PUT", array("Content" => "%F0%9F%91%8D"), "Content-length: 12", "Authorization: Bot $bot_token");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }
        /**
         * @Route("/discord/reaction/create_thread", name="discord_api_create_thread")
         */
        public function createThreadWithoutMessage(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $service = $service_repository->findByName("discord");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Discord: Service not found"), 404);
            }
            $service = $service[0];
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Discord: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Discord: Automation_action ID not found"), 404);
            }
            $informations = $automation_action->getInformations();
            if (empty($informations->name) || empty($informations->channel_id) || empty($informations->name)) {
                return new JsonResponse(array("message" => "Discord: Informations not found"), 404);
            }
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 3) {
                return new JsonResponse(array("message" => "Discord: Identifiers error"), 422);
            }
            $bot_token = $identifiers[2];
            // $url = "https://discord.com/api/v9/channels/$informations->channel_id/threads";
            if (strtolower($informations->type) === strtolower("public")) {
                $type = 11;
            } else if (strtolower($informations->type) === strtolower("private")) {
                $type = 12;
            } else {
                $type = 14;
            }
            $data = array(
                "name" => $informations->name,
                "type" => $type
            );
            // $jsonData = json_encode($data);
            // $headers = array(
            //     "Authorization: Bot $bot_token",
            //     "Content-Type: application/json",
            //     "Content-Length: " . strlen($jsonData)
            // );
            // $curl = curl_init();
            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => $url,
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => "",
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => "POST",
            //     CURLOPT_POSTFIELDS => $jsonData,
            //     CURLOPT_HTTPHEADER => $headers,
            // ));
            // $response = curl_exec($curl);
            // curl_close($curl);

            // Request to create the thread
            $response = $this->sendRequest("", "channels/$informations->channel_id/threads", "POST", $data, "Content-Length: " . strlen(json_encode($data)), "Authorization: Bot $bot_token");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }

        /**
         * @Route("/discord/test", name="discord_api_test")
         */
        public function test(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
        }
    }
?>