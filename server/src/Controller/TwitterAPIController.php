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
     * @Route("/twitter/a/p/i", name="app_twitter_a_p_i")
     */

    private const API_URL = "https://api.twitter.com/2/tweets";
    private RequestAPI $request_api;

    public function index(): Response
    {
        return $this->render('twitter_api/index.html.twig', [
            'controller_name' => 'TwitterAPIController',
        ]);
    }

    public function requestToken(Request $request, ServiceRepository $sevice_repository)
    {
        $service = $sevice_repository->findByName("twitter")[0];
        if (empty($service)) {
            return new JsonResponse(array("status" => "error")); //pas de service spotify en db
        }
        $identifiers = explode(";", $service->getIdentifiers());
        if (count($identifiers) != 2) {
            return new JsonResponse(array("status" => "error")); //probleme client id ou secret en db
        }

        $consumerKey = $identifiers[0];
        $consumerSecret = $identifiers[1];
        $accessToken = $identifiers[2];
        $accessTokenSecret = $identifiers[3];

        $bearerToken = base64_encode($consumerKey . ":" . $consumerSecret); // Génération token accès pour l'oauth

        $curl = curl_init(); // post requête pour le jeton d'accès
        curl_setopt($curl, CURLOPT_URL, "https://api.twitter.com/oauth2/token");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Basic " . $bearerToken,
            "Content-Type: application/x-www-form-urlencoded;charset=UTF-8"
        ]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        $response = curl_exec($curl);
        curl_close($curl);

        $accessToken = json_decode($response)->access_token; // check la reponse de la requête pour avoir le token d'accès
    }

    public function writeTweet($accessToken)
    {
        // todo utilisation de la fonction pour faire les requêtes
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.twitter.com/2/tweets");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $accessToken,
            "Content-Type: application/json"
        ]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
            "status" => "Ceci est un tweet test !"
        ]));
        $response = curl_exec($curl);
        curl_close($curl);

        if (json_decode($response)->data->status === "created") { // error handling de la publication du tweet
            echo "Tweet publié avec succès.";
        } else {
            echo "Erreur lors de la publication du tweet";
        }
    }

    private function sendTwitterAPIRequest($url, $method, $headers, $data = '') // headers c'est pour les tokens d'id et data c'est si il faut des données en plus pour la requête 
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($curl);

        // if ($response === false) {
        //     $error = curl_error($curl);
        //     curl_close($curl);
        //     throw new Exception("cURL request failed: $error"); // gérer les exceptions plus tard
        // }

        curl_close($curl);
        return $response;
    }
}
