<?php
    require_once("requester.php");
    require_once("artistService.php");
    require_once("trackService.php");
    require_once("classes/Spotify/album.class.php");

    function getAlbum($access_token, $decoded_data) {
        $artists = array();
        foreach ($decoded_data->artists as $artist) {
            array_push($artists, getArtistById($access_token, $artist->id));
        }
        $tracks = array();
        foreach ($decoded_data->tracks->items as $track) {
            array_push($tracks, getTrackById($access_token, $track->id));
        }
        $album = new Album( $artists,
                            $decoded_data->available_markets,
                            $decoded_data->external_urls,
                            $decoded_data->href,
                            $decoded_data->id,
                            $decoded_data->images,
                            $decoded_data->name,
                            $decoded_data->release_date,
                            $decoded_data->total_tracks,
                            $decoded_data->album_type,
                            $tracks
                        );
        return $album;
    }

    function getAlbumById($access_token, $album_id) {
        //check if "album_type" == "album" sinon mettre une erreur
        $decoded_data = requestAPI($access_token, "https://api.spotify.com/v1/albums/$album_id");
        
        // if $decoded_data["album_type"] == "single" -> error | A faire
        return getAlbum($access_token, $decoded_data);
    }

    function getAlbumBySearch($access_token, $searched_album) {// faire la meme mais en rajoutant l'artiste pour plus de précision
        $search = strtolower(str_replace(" ", "+", $searched_album));
        $decoded_data = requestAPI($access_token, "https://api.spotify.com/v1/search?type=album&q=$search");
        // if size > 0
        $tmp = 0;
        $found_album = Object;
        foreach ($decoded_data->albums->items as $album) {
            if (strcmp(strtolower($album->name), strtolower($searched_album)) === 0) {
                return (getAlbumById($access_token, $album->id));
            }
            if (abs(strcmp(strtolower($album->name), strtolower($searched_album))) < $tmp || $found_album === Object) {
                $tmp = abs(strcmp(strtolower($album->name), strtolower($searched_album)));
                $found_album = $album;
            }
        }
        return getAlbumById($access_token, $found_album->id);
    }
?>