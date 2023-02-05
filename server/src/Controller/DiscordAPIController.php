<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DiscordAPIController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('discord_api/index.html.twig', [
            'controller_name' => 'DiscordAPIController',
        ]);
    }

    /**
     * @Route("/discord/connect", name="discord_api_connect")
     */
    public function connect()
    {
        $redirect_uri = "";
        $client_id = "1070728516188000336";
        return $this->redirectToAutorisationLinka($client_id, $redirect_uri);
    }

    private function redirectToAutorisationLinka($client_id, $redirect_uri)
    {
        // Set the state when the request is good
        $state = "17";
        // Compose the authorization url
        $authorization_url = "https://discord.com/api/oauth2/authorize?client_id=$client_id&response_type=code&redirect_uri=$redirect_uri&scope=identify&state=$state";
        return $this->redirect($authorization_url);
    }

    /**
     * @Route("/discord/get_access_token", name="discord_api_get_access_token")
     */

    public function getAccessToken(Request $request)
    {
        // Get needed values
        if (empty($request->query->get("state"))) {
            return new JsonResponse("Spotify: Missing field", 400);
        }
        $state = $request->query->get("state");
        if ($state != "17") {
            return new JsonResponse("Spotify: Bad request to get access token", 400);
        }
        if (empty($request->query->get("code"))) {
            return new JsonResponse("Spotify: Missing field", 400);
        }
        $code = $request->query->get("code");
        $client_id = "1070728516188000336";
        $client_secret = "gyAZgaaAs2BWQHI4iGLbdjkaTUujNr7V";
        $redirect_uri = "http://localhost:8000/discord/get_access_token";
        // Request for the access token
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
        if (!isset(json_decode($result)->access_token)) {
            return new JsonResponse("Spotify: Bad code to get access token", 400);
        }
        return new JsonResponse(array("token" => json_decode($result)->access_token), 200);
    }

    /**
     * @Route("/discord/send_direct_message", name="discord_api_send_direct")
     */

    public function sendDirectMessageToRandomFriend($accessToken, $friendList)
    {
        // Pick a random friend from the friend list
        $randomFriend = $friendList[array_rand($friendList)];

        // Build the URL for sending a direct message to the friend
        $sendMessageUrl = 'https://discord.com/api/users/' . urlencode($randomFriend) . '/channels';

        // Build the request data
        $requestData = json_encode(array(
            'recipient_id' => $randomFriend
        ));

        // Build the headers for the request
        $headers = array(
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        );

        // Send the request to create a new direct message channel with the friend
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $sendMessageUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $requestData,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        // Decode the response
        $responseData = json_decode($response, true);

        // Get the ID of the direct message channel
        $channelId = $responseData['id'];

        // Build the URL for sending a message to the direct message channel
        $sendMessageUrl = 'https://discord.com/api/channels/' . urlencode($channelId) . '/messages';

        // Build the request data
        $requestData = json_encode(array(
            'content' => 'Hello, random friend!'
        ));

        // Send the request to send the message to the direct message channel
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $sendMessageUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $requestData,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }
}
