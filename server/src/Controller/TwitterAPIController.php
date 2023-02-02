<?php

namespace App\Controller;

use App\Entity\RequestAPI;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TwitterAPIController extends AbstractController
{
    /**
     * @Route("/twitter/get_access_token", name="twitter_api_get_access_token")
     */

    function requestAccessToken($consumerKey = "lUyeZYWIWdiZHVbGhx4OUqAgM", $consumerSecret = "FblKc0edbcywBMWIG6toFHC3zbEA11WTTvkNbw4A9TFj9dWKC0", $callbackUrl = "localhost:8000")
    {
        $auth = base64_encode($consumerKey . ':' . $consumerSecret);

        $postData = array(
            'grant_type' => 'client_credentials'
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.twitter.com/oauth2/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $auth,
                'Content-Type: application/x-www-form-urlencoded;charset=UTF-8'
            )
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
        } else {
            return json_decode($response);
        }
    }

    /**
     * @Route("/twitter/change_status", name="twitter_api_change_status")
     */

    function changeStatus($accessToken, $accessTokenSecret, $status = "Tweet de test !")
    {
        // Concaténation des jetons d'accès pour construire l'en-tête d'authentification
        $bearerToken = base64_encode($accessToken . ":" . $accessTokenSecret);
        $authorizationHeader = "Authorization: Bearer " . $bearerToken;

        // encode le contenu du tweet
        $status = urlencode($status);

        $ch = curl_init();
        // Configuration de l'URL de l'API Twitter avec l'endpoint pour la gestion des tweets
        curl_setopt($ch, CURLOPT_URL, "https://api.twitter.com/2/tweets");
        curl_setopt($ch, CURLOPT_POST, true);
        // données utilisé par la requếte
        curl_setopt($ch, CURLOPT_POSTFIELDS, "status=$status");
        // config du header
        curl_setopt($ch, CURLOPT_HTTPHEADER, [$authorizationHeader]);
        // Désactivation de la vérification SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Exécution de la requête
        $response = curl_exec($ch);
        curl_close($ch);

        // Analyse de la réponse de l'API pour déterminer si le tweet a été publié avec succès
        $responseObject = json_decode($response);
        if (isset($responseObject->id)) {
            return true;
        } else {
            return false;
        }
    }
}
