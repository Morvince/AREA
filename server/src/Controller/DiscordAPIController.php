<?php

namespace App\Controller;

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
    private $access_token;

    public function index(): Response
    {
        return $this->render('discord_api/index.html.twig', [
            'controller_name' => 'DiscordAPIController',
        ]);
    }

    /**
     * @Route("/discord/connect", name="discord_api_connect")
     */
    public function connect(ServiceRepository $service_repository)
    {
        $redirect_uri = "";
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
            "identify"
        ); // uniquement les permissions qui ont fonctionné ensemble (todo: check si il y en a plus possible)
        $scope = implode(" ", $scope);
        // Compose the authorization url
        $authorization_url = "https://discord.com/api/oauth2/authorize?client_id=$client_id&response_type=code&redirect_uri=$redirect_uri&scope=$scope&state=$state";
        return $this->redirect($authorization_url);
    }

    /**
     * @Route("/discord/get_access_token", name="discord_api_get_access_token")
     */

    public function getAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_sevice_repository)
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
        if (count($identifiers) != 2) {
            return new JsonResponse(array("message" => "Discord: Identifiers error"), 422);
        }
        $client_id = $identifiers[0];
        $client_secret = $identifiers[1];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://discord.com/api/oauth2/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "client_id=$client_id&client_secret=$client_secret&grant_type=authorization_code&code=$code&redirect_uri=$redirect_uri&scope=identify");
        curl_setopt($ch, CURLOPT_POST, true);
        $headers = array();
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);
        $this->access_token = json_decode($result);
        if (!isset(json_decode($result)->access_token)) {
            return new JsonResponse("Spotify: Bad code to get access token", 400);
        }
        return new JsonResponse(array("token" => json_decode($result)->access_token), 200);
    }

    /**
     * @Route("/discord/send_direct_message", name="discord_api_send_direct")
     */

    // public function sendDirectMessageToRandomFriend(ServiceRepository $service_repository, $friendList)
    // {

    //     // Pick a random friend from the friend list
    //     $randomFriend = $friendList[array_rand($friendList)];

    //     // Build the URL for sending a direct message to the friend
    //     $sendMessageUrl = 'https://discord.com/api/users/' . urlencode($randomFriend) . '/channels';

    //     // Build the request data
    //     $requestData = json_encode(array(
    //         'recipient_id' => $randomFriend
    //     ));

    //     // Build the headers for the request
    //     $headers = array(
    //         'Authorization: Bearer ' . $accessToken,
    //         'Content-Type: application/json'
    //     );

    //     // Send the request to create a new direct message channel with the friend
    //     $curl = curl_init();
    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => $sendMessageUrl,
    //         CURLOPT_POST => true,
    //         CURLOPT_POSTFIELDS => $requestData,
    //         CURLOPT_HTTPHEADER => $headers,
    //         CURLOPT_RETURNTRANSFER => true
    //     ));
    //     $response = curl_exec($curl);
    //     curl_close($curl);

    //     // Decode the response
    //     $responseData = json_decode($response, true);

    //     // Get the ID of the direct message channel
    //     $channelId = $responseData['id'];

    //     // Build the URL for sending a message to the direct message channel
    //     $sendMessageUrl = 'https://discord.com/api/channels/' . urlencode($channelId) . '/messages';

    //     // Build the request data
    //     $requestData = json_encode(array(
    //         'content' => 'Hello, random friend!'
    //     ));

    //     // Send the request to send the message to the direct message channel
    //     $curl = curl_init();
    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => $sendMessageUrl,
    //         CURLOPT_POST => true,
    //         CURLOPT_POSTFIELDS => $requestData,
    //         CURLOPT_HTTPHEADER => $headers,
    //         CURLOPT_RETURNTRANSFER => true
    //     ));
    //     $response = curl_exec($curl);
    //     curl_close($curl);
    // }

    /**
     * @Route("/discord/change_nickname_on_discord", name="discord_api_change_nickname")
     */

    public function changeNicknameOnDiscord(ServiceRepository $service_repository)
    {
        // Définissez le nouveau pseudo désiré à mettre en argument quand on va recup un nom d'artiste ou autre par exemple.
        $new_username = "Morvince'API";

        // Récupérez le jeton d'accès
        $token = $this->access_token; // remplacer par le token d'accès et le recup avec la db

        // Préparez la requête PATCH pour mettre à jour le pseudo
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://discord.com/api/v6/users/@me",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PATCH",
            CURLOPT_POSTFIELDS => "{\"username\":\"$new_username\"}",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $token",
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
    }
}
