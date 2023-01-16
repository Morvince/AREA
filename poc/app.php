<?php
	require_once("services/Spotify/albumService.php");
	require_once("services/Spotify/artistService.php");

    echo "<pre>";

        // Définir les variables d'authentification
        $client_id = "ID";
        $client_secret = "SECRET";

        // Demander un jeton d'accès
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://accounts.spotify.com/api/token");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials&client_id=$client_id&client_secret=$client_secret");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        curl_close ($ch);

        // Décoder la réponse JSON et extraire le jeton d'accès
        $response = json_decode($server_output);
        $access_token = $response->access_token;

    if (!empty($_GET["artist"])) {
        $artist = getArtistBySearch($access_token, $_GET["artist"]);

        echo "<img src=".$artist->getImages()[0]->url."><br>";
        echo "Artiste: ".$artist->getName()."<br>Albums:<br><br>";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/artists/".$artist->getId()."/albums");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$access_token));
        $data = curl_exec($ch);
        curl_close($ch);

        $decoded_data = json_decode($data);
        foreach ($decoded_data->items as $item) {
            echo "$item->name - $item->release_date<br><img src=".$item->images[1]->url."><br><br>";
        }
    } else if (!empty($_GET["album"])) {
        $album = getAlbumBySearch($access_token, $_GET["album"]);

        echo "<img src=".$album->getImages()[0]->url."><br>";
        echo "Album: ".$album->getName()." release by ".$album->getArtists()[0]->getName()." the ".$album->getReleaseDate();
    } else {
    //afficher les catégories en france
        $decoded_data = requestAPI($access_token, "https://api.spotify.com/v1/browse/categories?country=FR&locale=fr_FR");
        foreach ($decoded_data->categories->items as $item) {
            echo "Category name: $item->name<br>";
        }
    }

    echo "</pre>";
?>

<!--
// demander les autorisations à un autre compte
    // Définir les variables d'authentification
    $redirect_uri = "127.0.0.1/area/app.php";
    $scope = "user-library-read user-library-modify";
    // Générer le lien d'autorisation
    $auth_link = "https://accounts.spotify.com/authorize?client_id=$client_id&response_type=code&redirect_uri=$redirect_uri&scope=$scope";
    // Rediriger l'utilisateur vers le lien d'autorisation
    header("Location: $auth_link");
-->