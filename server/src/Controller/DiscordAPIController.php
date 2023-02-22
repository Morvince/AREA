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
    { // a changer pour lutiliser que via le server
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
    { // a changer pour lutiliser que via le server
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
    private function sendRequest($access_token, $endpoint, $method = "GET", $parameters = array())
    {
        if (empty($this->request_api)) {
            $this->request_api = new RequestAPI();
        }
        $response = $this->request_api->send($access_token, self::API_URL . $endpoint, $method, $parameters);
        if (isset(json_decode($response)->error)) {
            switch (json_decode($response)->error->status) {
                case 400:
                    $response = json_encode(array("message" => "Discord: Bad request", "code" => 400));
                    break;
                case 401:
                    $response = json_encode(array("message" => "Discord: Bad or expired token", "code" => 401));
                    break;
                case 403:
                    $response = json_encode(array("message" => "Discord: Forbidden", "code" => 403));
                    break;
                case 404:
                    $response = json_encode(array("message" => "Discord: Ressource not found", "code" => 404));
                    break;
                case 429:
                    $response = json_encode(array("message" => "Discord: Too many requests", "code" => 429));
                    break;
                case 500:
                    $response = json_encode(array("message" => "Discord: Internal server error", "code" => 500));
                    break;
                case 502:
                    $response = json_encode(array("message" => "Discord: Bad gateway", "code" => 502));
                    break;
                case 503:
                    $response = json_encode(array("message" => "Discord: Service unavailable", "code" => 503));
                    break;
                default:
                    break;
            }
        }
        return $response;
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
        $old_username = $request_content->old->username;
        $new_username = $request_content->new->username;
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
        $username = json_decode($this->sendRequest($access_token, "users/@me"));
        if (isset($username->code)) {
            return new JsonResponse(array("message" => $username->message), $username->code);
        }
        $username = $username->username;
        return new JsonResponse(array("username" => $username), 200);
    }

    /**
     * @Route("/discord/action/check_user_join", name="discord_api_check_user_join")
     */
    public function isUserJoined(Request $request)
    {
        // Get needed values
        $request_content = json_decode($request->getContent());
        if (empty($request_content->new) || empty($request_content->old)) {
            return new JsonResponse(array("message" => "Discord: Missing field"), 400);
        }
        $old_users = $request_content->old->users;
        $new_users = $request_content->new->users;
        // Check if user join a server
        foreach ($new_users as $new_user) {
            $found = false;
            foreach ($old_users as $old_user) {
                if (strcmp($new_user->users->id, $old_user->users->id) === 0) {
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
     * @Route("/discord/action/check_user_join/get_parameters", name="discord_api_check_user_join_parameters")
     */
    public function getIsUserJoinParameters(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
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
        $informations = json_decode($automation_action->getInformations());
        if (empty($informations->guild_id)) {
            return new JsonResponse(array("message" => "Discord: Guild ID not found"), 404);
        }
        // potentiellement faire avec le bot token si il n'y a pas de résultat avec le access token
        $users = json_decode($this->sendRequest($access_token, "guilds/$informations->guild_id/members"));
        if (isset($users->code)) {
            return new JsonResponse(array("message" => $users->message), $users->code);
        }
        return new JsonResponse(array("users" => $users), 200);
    }

    /**
     * @Route("/discord/get_user_channels", name="discord_api_get_user_channels")
     */
    public function getUserChannels(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
    {
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
        $service = $service_repository->findByName("discord");
        if (empty($service)) {
            return new JsonResponse(array("message" => "Discord: Service not found"), 404);
        }
        $service = $service[0];
        if (empty($user_service_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
            return new JsonResponse(array("message" => "Discord: Access token not found"), 404);
        }
        $identifiers = explode(";", $service->getIdentifiers());
        if (count($identifiers) != 3) {
            return new JsonResponse(array("message" => "Discord: Identifiers error"), 422);
        }
        $access_token = $user_service_repository->findByUserIdAndServiceId($user_id, $service->getId())[0]->getAccessToken();
        // Request for the user server
        $bot_token = $identifiers[2]; // recup du token avec l'identif 2
        $guilds_url = 'https://discord.com/api/users/@me/guilds';
        $guilds_headers = [
            'Authorization: Bot ' . $bot_token,
            'Content-Type: application/json',
        ];
        $guilds_options = [
            'http' => [
                'header' => $guilds_headers,
                'method' => 'GET',
            ],
        ];
        // pour récup la liste des id des serveurs
        $guilds_context = stream_context_create($guilds_options);
        $guilds_response = file_get_contents($guilds_url, false, $guilds_context);
        $guilds_data = json_decode($guilds_response, true);

        // Récupérer l'ID du premier serveur et en set un random
        $guild_id = $guilds_data[0]['id'];

        // faire la même récup mais avec les channels
        $channels_url = "https://discord.com/api/guilds/$guild_id/channels";
        $channels_headers = [
            'Authorization: Bot ' . $bot_token,
            'Content-Type: application/json',
        ];
        $channels_options = [
            'http' => [
                'header' => $channels_headers,
                'method' => 'GET',
            ],
        ];
        $channels_context = stream_context_create($channels_options);
        $channels_response = file_get_contents($channels_url, false, $channels_context);
        $channels_data = json_decode($channels_response, true);

        $allowed_channels = array();
        $bot_id = 0; // add l'id du bot

        // Vérifier les permissions pour chaque canal
        foreach ($channels_data as $channel) {
            $permissions_url = "https://discord.com/api/channels/{$channel['id']}/permissions/{$bot_id}";
            $permissions_headers = [
                'Authorization: Bot ' . $bot_token,
                'Content-Type: application/json',
            ];
            $permissions_options = [
                'http' => [
                    'header' => $permissions_headers,
                    'method' => 'GET',
                ],
            ];
            $permissions_context = stream_context_create($permissions_options);
            $permissions_response = file_get_contents($permissions_url, false, $permissions_context);
            $permissions_data = json_decode($permissions_response, true);

            if ($permissions_data['allow'] & 1024) {
                $allowed_channels[] = $channel['id'];
            }
        }
        return new JsonResponse(array("channels" => $allowed_channels), 200);
    }

    // Reaction
    /**
     * @Route("/discord/reaction/send_channel_message", name="discord_api_send_channel_message")
     */
    public function sendChannelMessage(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
    {
        // Récupérez le jeton d'accès
        $request_content = json_decode($request->getContent()); // début de la récup du field informations link au fiels qui est link au front
        if (empty($request_content->automation_action_id)) {
            return new JsonResponse(array("message" => "Discord: Missing field"), 400);
        }
        $automation_action_id = $request_content->automation_action_id;
        $automation_action = $automation_action_repository->find($automation_action_id);
        if (empty($automation_action)) {
            return new JsonResponse(array("message" => "Discord: Automation_action ID not found"), 404);
        }
        $informations = $automation_action->getInformations(); // attribution des valeurs d'informations dans la var info
        if (empty($informations->channel_id) && empty($informations->message)) {
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
        $result = $this->request_api->send($access_token, "https://discordapp.com/api/v6/users/@me", "GET", array());
        $username = json_decode($result)->id;
        $name = json_decode($result)->username;
        $bot_token = $identifiers[2];
        // return new JsonResponse(json_decode($result)); // pour check si on peut recup d'autres infos sur le user
        $message = $informations->message; // set le message de l'utilisateur, lui proposer des phrases si c'est Discord ou autre chose
        $tag_message = "<@$username>"; // début du message qui identifie l'utilisateur en question
        $message = $tag_message . $message; // concaténation pour le message final qui identifie bien l'user
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://discordapp.com/api/v6/channels/$informations->channel_id/messages",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode(array("content" => $message)),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bot $bot_token",
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return new JsonResponse(array("token" => json_decode($response)), 200);
    }

    /**
     * @Route("/discord/test", name="discord_api_test")
     */
    public function test(Request $request, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository)
    {
        $parameters = array("automation_action_id" => 2);
        $url = "http://localhost/discord/reaction/send_private";
        if (empty($this->request_api)) {
            $this->request_api = new RequestAPI();
        }
        $response = $this->request_api->sendRoute($url, $parameters);
        $response = json_decode($response);
        if (isset($response->code)) {
            return new JsonResponse(array("message" => $response->message), $response->code);
        }
        return new JsonResponse(array("response" => $response), 200);
    }

    /**
     * @Route("/discord/reaction/send_private", name="discord_api_send_private")
     */

    public function sendPrivateMessage(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
    {
        // Récupérez le jeton d'accès
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
        $automation = $automation_repository->find($automation_action->getAutomationId());
        if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
            return new JsonResponse(array("message" => "Discord: Access token not found"), 404);
        }
        $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
        if (empty($this->request_api)) {
            $this->request_api = new RequestAPI();
        }
        $informations = $automation_action->getInformations(); // attribution des valeurs d'informations dans la var info
        if (empty($informations->user_id) && empty($informations->guild_id)) {
            return new JsonResponse(array("message" => "Discord: Informations not found"), 404);
        }
        $identifiers = explode(";", $service->getIdentifiers());
        if (count($identifiers) != 3) {
            return new JsonResponse(array("message" => "Discord: Identifiers error"), 422);
        }
        $bot_token = $identifiers[2];
        $headers = array(
            "Authorization: Bot $bot_token",
            "Content-Type: application/json"
        );

        // Création d'un canal privé entre le bot et l'ami
        $data = array("recipient_id" => $informations->user_id);
        $ch = curl_init("https://discord.com/api/v9/users/@me/channels");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $channelData = json_decode($response, true);
        $channelId = $channelData["id"];

        $data = array("content" => $informations->message);
        $ch = curl_init("https://discord.com/api/v9/channels/$channelId/messages");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);

        return new JsonResponse(array(json_decode($response)), 200);
    }

    /**
     * @Route("/discord/reaction/react_message", name="discord_api_react_message")
     */

    public function reactWithMessage(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
    {
        header('Access-Control-Allow-Origin: *');
        // Récupérez le jeton d'accès
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
        $automation = $automation_repository->find($automation_action->getAutomationId());
        if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
            return new JsonResponse(array("message" => "Discord: Access token not found"), 404);
        }
        $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
        if (empty($this->request_api)) {
            $this->request_api = new RequestAPI();
        }
        $informations = $automation_action->getInformations(); // attribution des valeurs d'informations dans la var info
        if (empty($informations->reaction) && empty($informations->channel_id)) {
            return new JsonResponse(array("message" => "Discord: Informations not found"), 404);
        }
        $identifiers = explode(";", $service->getIdentifiers());
        if (count($identifiers) != 3) {
            return new JsonResponse(array("message" => "Discord: Identifiers error"), 422);
        }
        $bot_token = $identifiers[2];
        $url = "https://discord.com/api/v9/channels/$informations->channel_id/messages?limit=1";
        $headers = array(
            "Authorization: Bot $bot_token",
            "Content-Type: application/json",
            "Content-length: 0"
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($response, true);
        if (count($data) === 0) {
            return "Error: No messages found in channel $informations->channel_id.";
        }

        $last_message_id = $data[0]['id'];
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
        $url = "https://discord.com/api/v9/channels/$informations->channel_id/messages/$last_message_id/reactions/$reaction/@me";

        $header = array(
            "Authorization: Bot $bot_token",
            "Content-Type: application/json",
            "Content-length: 12"
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_POSTFIELDS => json_encode(array("Content" => "%F0%9F%91%8D")),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return new JsonResponse(array("token" => json_decode($response)), 200);
    }

    /**
     * @Route("/discord/reaction/create_thread", name="discord_api_create_thread")
     */
    public function createThreadWithoutMessage(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
    {
        header('Access-Control-Allow-Origin: *');
        // Récupérez le jeton d'accès
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
        $automation = $automation_repository->find($automation_action->getAutomationId());
        if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
            return new JsonResponse(array("message" => "Discord: Access token not found"), 404);
        }
        $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
        if (empty($this->request_api)) {
            $this->request_api = new RequestAPI();
        }
        $informations = $automation_action->getInformations(); // attribution des valeurs d'informations dans la var info
        if (empty($informations->reaction) && empty($informations->channel_id)) {
            return new JsonResponse(array("message" => "Discord: Informations not found"), 404);
        }
        $identifiers = explode(";", $service->getIdentifiers());
        if (count($identifiers) != 3) {
            return new JsonResponse(array("message" => "Discord: Identifiers error"), 422);
        }
        $bot_token = $identifiers[2];
        $url = "https://discord.com/api/v9/channels/$informations->channel_id/threads";

        if (strtolower($informations->type) === strtolower("public")) {
            $type = 11;
        } else if (strtolower($informations->type) === strtolower("private")) {
            $type = 12;
        } else
            $type = 14; // valeur par défaut en mode news thread

        $data = array(
            'name' => $informations->name,
            'type' => $type
        );
        $jsonData = json_encode($data);
        $headers = array(
            "Authorization: Bot $bot_token",
            "Content-Type: application/json",
            "Content-Length: " . strlen($jsonData)
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => $headers,
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $response_data = json_decode($response, true);

        return new JsonResponse(array("token" => json_decode($response)), 200);
    }
}
