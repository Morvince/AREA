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
     * @brief This function is used to connect the service with the user account 
     * 
     * @param[in] request used to get the content of the old username and the new one
     * @param[in] service_repo to access linked values on database
     * @return JsonReponse will return a Json object containing all the information when the action is working
     */

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
     * @Route("/discord/refresh_access_token", name="discord_api_refresh_access_token")
     */
    public function refreshAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
    {
        // Get needed values
        $request_content = json_decode($request->getContent());
        if (empty($request_content->access_token)) {
            return new JsonResponse(array("message" => "Discord: Missing field"), 400);
        }
        $access_token = $request_content->access_token;
        $service = $service_repository->findByName("discord");
        if (empty($service)) {
            return new JsonResponse(array("message" => "Discord: Service not found"), 404);
        }
        $service = $service[0];
        $identifiers = explode(";", $service->getIdentifiers());
        if (count($identifiers) != 3) {
            return new JsonResponse(array("message" => "Discord: Identifiers error"), 422);
        }
        if (empty($user_service_repository->findBy(array("access_token" => $access_token)))) {
            return new JsonResponse(array("message" => "Discord: Refresh token not found"), 404);
        }
        $client_id = $identifiers[0];
        $client_secret = $identifiers[1];
        $user_service = $user_service_repository->findBy(array("access_token" => $access_token))[0];
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
            $user_service_repository->remove($user_service, true);
            return new JsonResponse(array("message" => "Discord: Expired refresh token"), 400);
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

    private function sendRequest($access_token, $endpoint, $method = "GET", $parameters = array(), $added_header = null, $authorization = "")
    {
        if (empty($this->request_api)) {
            $this->request_api = new RequestAPI();
        }
        $response = json_decode($this->request_api->send($access_token, self::API_URL . $endpoint, $method, $parameters, $added_header, $authorization));
        if (isset($response->code)) {
            if ($access_token === "") {
                $this->request_api->sendRoute("http://localhost/discord/refresh_access_token", array("access_token" => $access_token));
            }
            $response = array("message" => "Discord: $response->message with code $response->code", "code" => 400);
        }
        return json_decode(json_encode($response));
    }

    /**
     * @brief This function will allow to get all the usable channels for controller functions
     * 
     * @param[in] request used to get the content of the old username and the new one
     * @param[in] automation_repo to access linked values on database
     * @param[in] automation_action_repo to access linked values on database
     * @param[in] service_repo to access linked values on database
     * @param[in] user_service_repo to access linked values on database
     * @return JsonReponse will return a Json object containing all the information when working
     */

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
        $access_token = $user_service_repository->findByUserIdAndServiceId($user_id, $service->getId())[0]->getAccessToken();
        $identifiers = explode(";", $service->getIdentifiers());
        if (count($identifiers) != 3) {
            return new JsonResponse(array("message" => "Discord: Identifiers error"), 422);
        }
        $bot_token = $identifiers[2];
        // Request to get bot servers
        $response = $this->sendRequest("", "users/@me/guilds", "GET", null, null, "Authorization: Bot $bot_token");
        if (isset($response->code)) {
            return new JsonResponse(array("message" => $response->message), $response->code);
        }
        $guilds = $response;
        $bot_guilds = array();
        foreach ($guilds as $guild) {
            array_push($bot_guilds, $guild->id);
        }
        // Request to get user servers
        $response = $this->sendRequest($access_token, "users/@me/guilds", "GET");
        if (isset($response->code)) {
            return new JsonResponse(array("message" => $response->message), $response->code);
        }
        $data = $response;
        $user_guilds = array();
        foreach ($data as $guild) {
            if (isset($guild->id)) {
                $user_guilds[] = $guild->id;
            }
        }
        // Keep only servers where the bot and the user are 
        $common_guilds = array_intersect($user_guilds, $bot_guilds);
        $choosen_guild = current($common_guilds);
        // Request to get server info
        $response = $this->sendRequest("", "guilds/$choosen_guild", "GET", null, null, "Authorization: Bot $bot_token");
        if (isset($response->code)) {
            return new JsonResponse(array("message" => $response->message), $response->code);
        }
        $server_info = $response;
        $name_server = $server_info->name;
        // Request to get server channels
        $response = $this->sendRequest("", "guilds/$choosen_guild/channels", "GET", null, null, "Authorization: Bot $bot_token");
        if (isset($response->code)) {
            return new JsonResponse(array("message" => $response->message), $response->code);
        }
        $channels = $response;
        $text_channels = array();
        foreach ($channels as $channel) {
            if ($channel->type == 0) {
                $text_channels[] = array(
                    "id" => $channel->id,
                    "name" => $name_server . " - " . $channel->name
                );
            }
        }
        return new JsonResponse(array("items" => $text_channels), 200);
    }

    /**
     * @brief This function is used to get the type for the thread before creation
     * 
     * @return JsonReponse will return a Json object containing all the information when the it's working
     */

    /**
     * @Route("/discord/get_thread_type", name="discord_api_get_thread_type")
     */
    public function getThreadType()
    {
        return new JsonResponse(array("items" => array("name" => "public", "id" => 11), array("name" => "private", "id" => 12), array("name" => "news", "id" => 14)), 200);
    }

    // Action
    /**
     * @brief This function represent an action for the discord controller, it will check if the username change to trigger reaction
     * 
     * @param[in] request used to get the content of the old username and the new one
     * @return JsonReponse will return a Json object containing all the information when the action is working
     */

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
     * @brief This function is used to get the parameters for the username action
     * 
     * @param[in] request used to get the content of the old username and the new one
     * @param[in] automation_repo to access linked values on database
     * @param[in] automation_action_repo to access linked values on database
     * @param[in] service_repo to access linked values on database
     * @param[in] user_service_repo to access linked values on database
     * @return JsonReponse will return a Json object containing all the information when the action is working
     */

    /**
     * @Route("/discord/action/check_username/get_parameters", name="discord_api_check_username_get_parameters")
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
     * @brief This function is an reaction and it will use a request to send a channel message in discord server
     * 
     * @param[in] request used to get the content of the old username and the new one
     * @param[in] automation_repo to access linked values on database
     * @param[in] automation_action_repo to access linked values on database
     * @param[in] service_repo to access linked values on database
     * @param[in] user_service_repo to access linked values on database
     * @return JsonReponse will return a Json object containing all the information when the reaction is working
     */

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
        // Request to send a message on a channel
        $response = $this->sendRequest("", "channels/$informations->channel_id/messages", "POST", array("content" => $message), null, "Authorization: Bot $bot_token");
        if (isset($response->code)) {
            return new JsonResponse(array("message" => $response->message), $response->code);
        }
        return new JsonResponse(array("message" => "OK"), 200);
    }

    /**
     * @brief This function is an reaction and it will use a request to send private message to a user linked to the bot
     * 
     * @param[in] request used to get the content of the old username and the new one
     * @param[in] automation_repo to access linked values on database
     * @param[in] automation_action_repo to access linked values on database
     * @param[in] service_repo to access linked values on database
     * @param[in] user_service_repo to access linked values on database
     * @return JsonReponse will return a Json object containing all the information when the reaction is working
     */

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
        $data = array("recipient_id" => $informations->user_id);
        // Request to create the conversation
        $response = $this->sendRequest("", "users/@me/channels", "POST", $data, null, "Authorization: Bot $bot_token");
        if (isset($response->code)) {
            return new JsonResponse(array("message" => $response->message), $response->code);
        }
        $channel_id = $response->id;
        $data = array("content" => $informations->message);
        // Request to send the private message
        $response = $this->sendRequest("", "channels/$channel_id/messages", "POST", $data, null, "Authorization: Bot $bot_token");
        if (isset($response->code)) {
            return new JsonResponse(array("message" => $response->message), $response->code);
        }
        return new JsonResponse(array("message" => "OK"), 200);
    }

    /**
     * @brief This function is an reaction and it will use a request to react a message with random emoji in discord server last message
     * 
     * @param[in] request used to get the content of the old username and the new one
     * @param[in] automation_repo to access linked values on database
     * @param[in] automation_action_repo to access linked values on database
     * @param[in] service_repo to access linked values on database
     * @param[in] user_service_repo to access linked values on database
     * @return JsonReponse will return a Json object containing all the information when the reaction is working
     */

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
        //  Request to get the last message
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
        $url = "https://discord.com/api/v9/channels/$informations->channel_id/messages/$last_message_id/reactions/$reaction/@me";
        $header = array(
            "Authorization: Bot $bot_token",
            "Content-Type: application/json",
            "Content-length: 12"
        );
        // Request to react to the message
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
        curl_exec($curl);
        curl_close($curl);
        return new JsonResponse(array("message" => "OK"), 200);
    }


    /**
     * @brief This function is an reaction and it will use a request to create a new thread in discord server
     * 
     * @param[in] request used to get the content of the old username and the new one
     * @param[in] automation_repo to access linked values on database
     * @param[in] automation_action_repo to access linked values on database
     * @param[in] service_repo to access linked values on database
     * @param[in] user_service_repo to access linked values on database
     * @return JsonReponse will return a Json object containing all the information when the reaction is working
     */

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
        $data = array(
            "name" => $informations->name,
            "type" => $informations->type
        );
        // Request to create the thread
        $response = $this->sendRequest("", "channels/$informations->channel_id/threads", "POST", $data, "Content-Length: " . strlen(json_encode($data)), "Authorization: Bot $bot_token");
        if (isset($response->code)) {
            return new JsonResponse(array("message" => $response->message), $response->code);
        }
        return new JsonResponse(array("message" => "OK"), 200);
    }
}
