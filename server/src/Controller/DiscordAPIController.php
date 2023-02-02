<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiscordAPIController extends AbstractController
{
    /**
     * @Route("/discord/connect", name="discord_api_connect")
     */
    public function index(): Response
    {
        return $this->render('discord_api/index.html.twig', [
            'controller_name' => 'DiscordAPIController',
        ]);
    }

    // Function to get the access token

    public function getAccessToken($clientId, $clientSecret, $redirectUri, $code)
    {
        // Build the token URL
        $tokenUrl = 'https://discord.com/api/oauth2/token'
            . '?client_id=' . urlencode($clientId)
            . '&client_secret=' . urlencode($clientSecret)
            . '&grant_type=authorization_code'
            . '&code=' . urlencode($code)
            . '&redirect_uri=' . urlencode($redirectUri);

        // Get the response from the token URL
        $response = file_get_contents($tokenUrl);
        $response = json_decode($response, true);

        // Return the access token
        return $response['access_token'];
    }

    // Function to redirect the user to the Discord authorization URL
    public function redirectToDiscordAuthorization($clientId, $redirectUri)
    {
        // Build the authorization URL
        $authorizeUrl = 'https://discord.com/api/oauth2/authorize'
            . '?client_id=' . urlencode($clientId)
            . '&redirect_uri=' . urlencode($redirectUri)
            . '&response_type=code'
            . '&scope=' . urlencode('identify');

        // Redirect the user to the authorization URL
        header('Location: ' . $authorizeUrl);
        exit();
    }

    public function connect()
    {

        $redirectUri = "https://discord.com/api/oauth2/authorize?client_id=1070728516188000336&redirect_uri=http%3A%2F%2Flocalhost%3A8000%2Fdiscord%2Fget_access_token&response_type=code&scope=identify%20guilds%20rpc.voice.read%20gdm.join%20applications.builds.upload%20applications.store.update%20activities.write%20dm_channels.read%20applications.commands.permissions.update%20role_connections.write%20relationships.read%20applications.entitlements%20applications.builds.read%20webhook.incoming%20rpc.voice.write%20rpc%20guilds.join%20email%20guilds.members.read%20connections%20rpc.notifications.read%20rpc.activities.write%20messages.read%20applications.commands%20activities.read%20voice";
        $clientId = "1070728516188000336";
        $clientSecret = "gyAZgaaAs2BWQHI4iGLbdjkaTUujNr7V";
        $code = $_GET['code'];

        if (!isset($_GET['code'])) {
            // If not, redirect the user to the authorization URL
            $this->redirectToDiscordAuthorization($clientId, $redirectUri);
        } else {
            // If the code is set, get the access token
            $code = $_GET['code'];
            $accessToken = $this->getAccessToken($clientId, $clientSecret, $redirectUri, $code);

            // Use the access token to make API requests
            $userDetailsUrl = 'https://discord.com/api/users/@me';
            $headers = array(
                'Authorization: Bearer ' . $accessToken
            );

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $userDetailsUrl,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true
            ));
            $userDetails = curl_exec($curl);
            curl_close($curl);

            $userDetails = json_decode($userDetails, true);
        }
    }

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
