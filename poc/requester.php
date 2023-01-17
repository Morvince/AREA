<?php
    // Utiliser le jeton d'accès pour faire une demande à l'API
    function requestAPI($access_token, $request) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $access_token"));
        $data = curl_exec($ch);
        curl_close($ch);
        return (json_decode($data));
    }
?>