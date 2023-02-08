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
    private RequestAPI $request_api;

    /**
     * @Route("/discord/connect", name="discord_api_connect")
     */
    public function connect(ServiceRepository $service_repository)
    {
        $redirect_uri = "http://localhost:8000/discord/get_access_token";
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
        return $this->redirectToAutorisationLinka($client_id, $redirect_uri);
    }
    private function redirectToAutorisationLinka($client_id, $redirect_uri)
    {
        // Set the state when the request is good
        $state = "17";
        $scope = array(
            "email", "guilds",
            "connections", "messages.read",
            "identify", "gdm.join", "bot"
        ); // uniquement les permissions qui ont fonctionné ensemble (todo: check si il y en a plus possible)
        $scope = implode(" ", $scope);
        // Compose the authorization url
        $authorization_url = "https://discord.com/api/oauth2/authorize?client_id=$client_id&response_type=code&redirect_uri=$redirect_uri&scope=$scope&state=$state";
        return $this->redirect($authorization_url);
    }
    /**
     * @Route("/discord/get_access_token", name="discord_api_get_access_token")
     */
    public function getAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
    {
        // Get needed values
        if (empty($request->query->get("state"))) {
            return new JsonResponse("Discord: Missing field", 400);
        }
        $state = $request->query->get("state");
        if ($state != "17") {
            return new JsonResponse("Discord: Bad request to get access token", 400);
        }
        if (empty($request->query->get("code"))) {
            return new JsonResponse("Discord: Missing field", 400);
        }
        $code = $request->query->get("code");
        $redirect_uri = "http://localhost:8000/discord/get_access_token";
        // Request for the access token
        $service = $service_repository->findByName("discord");
        if (empty($service)) {
            return new JsonResponse(array("message" => "Discord: Service not found"), 404);
        }
        $service = $service[0];
        $identifiers = explode(";", $service->getIdentifiers());
        if (count($identifiers) != 3) {
            return new JsonResponse(array("message" => "Discord: Identifiers error"), 422);
        }
        $user_id = 1;
        $client_id = $identifiers[0];
        $client_secret = $identifiers[1];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://discord.com/api/oauth2/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "client_id=$client_id&client_secret=$client_secret&grant_type=authorization_code&code=$code&redirect_uri=$redirect_uri");
        curl_setopt($ch, CURLOPT_POST, true);
        $headers = array();
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);
        if (!isset(json_decode($result)->access_token)) {
            return new JsonResponse("Discord: Bad code to get access token", 400);
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
        return new JsonResponse(array("message" => "OK"), 200);
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
        $result = $this->getUsername($access_token);
        $username = json_decode($result)->username;
        if (isset($username->code)) {
            return new JsonResponse(array("message" => $username->message), $username->code);
        }
        return new JsonResponse(array("username" => $username), 200);
    }
    private function getUsername($access_token)
    {
        if (empty($this->request_api)) {
            $this->request_api = new RequestAPI();
        }
        $result = $this->request_api->send($access_token, "https://discordapp.com/api/v6/users/@me", "GET", array());
        $username = json_decode($result)->username;
        return $result;
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
        $old_users = $request_content->old->users->items;
        $new_users = $request_content->new->users->items;
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
        return new JsonResponse(array("message" => true), 200);
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
        $user_list = json_decode($this->getUserList($access_token, $informations->guild_id));
        if (isset($user_list->code)) {
            return new JsonResponse(array("message" => $user_list->message), $user_list->code);
        }
        return new JsonResponse(array("user_list" => $user_list), 200);
    }
    private function getUserList($access_token, $guild_id)
    {
        if (empty($this->request_api)) {
            $this->request_api = new RequestAPI();
        }
        $user_list = $this->request_api->send($access_token, "https://discordapp.com/api/guilds/$guild_id/members", "GET", array());
        return $user_list;
    }

    private function getChannelsAndGuildID(ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
    {
        $service = $service_repository->findByName("discord");
        if (empty($service)) {
            return new JsonResponse(array("message" => "Discord: Service not found"), 404);
        }
        $service = $service[0];
        if (empty($user_service_repository->findByUserIdAndServiceId(1, $service->getId()))) {
            return new JsonResponse(array("message" => "Discord: Access token not found"), 404);
        }
        $access_token = $user_service_repository->findByUserIdAndServiceId(1, $service->getId())[0]->getAccessToken();
        // Récupération de la liste des guildes associées au bot
        $guilds = $this->request_api->send($access_token, "https://discordapp.com/api/v6/users/@me/guilds", "GET", array());
        $guilds = json_decode($guilds);

        // Boucle sur les guildes pour récupérer les channels associés au bot
        $channels = array();
        foreach ($guilds as $guild) {
            $response = $this->request_api->send($access_token, "https://discordapp.com/api/v6/guilds/" . $guild->id . "/channels", "GET", array());
            $channels = array_merge($channels, json_decode($response));
        }
    }

    // Reaction
    /**
     * @Route("/discord/reaction/send_channel_message", name="discord_api_send_channel_message")
     */

    public function sendChannelMessage(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
    {
        // Récupérez le jeton d'accès
        $service = $service_repository->findByName("discord");
        if (empty($service)) {
            return new JsonResponse(array("message" => "Discord: Service not found"), 404);
        }
        $service = $service[0];
        if (empty($user_service_repository->findByUserIdAndServiceId(1, $service->getId()))) {
            return new JsonResponse(array("message" => "Discord: Access token not found"), 404);
        }
        $access_token = $user_service_repository->findByUserIdAndServiceId(1, $service->getId())[0]->getAccessToken();

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

        $automation_action_id = 2;
        $automation_action = $automation_action_repository->find($automation_action_id);
        if (empty($automation_action)) {
            return new JsonResponse(array("message" => "Discord: automation_action ID not found"), 404);
        }
        $informations = json_decode($automation_action->getInformations());
        if (empty($informations->channel_id)) {
            return new JsonResponse(array("message" => "Discord: Channel ID not found"), 404);
        }

        $message = "<@$username>! are you the real $name";

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
            CURLOPT_POSTFIELDS => "{\"content\":\"$message\"}",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bot $bot_token",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
        return new JsonResponse(array("token" => json_decode($response)), 200);
    }
}
