<?php
    require_once("requester.php");
    require_once("classes/Spotify/artist.class.php");

    function getArtist($decoded_data) {
        $artist = new Artist($decoded_data->external_urls,
                            $decoded_data->followers->total,
                            $decoded_data->genres,
                            $decoded_data->href,
                            $decoded_data->id,
                            $decoded_data->images,
                            $decoded_data->name,
                            $decoded_data->popularity,
                            $decoded_data->uri
                        );
        return $artist;
    }

    function getArtistById($access_token, $artist_id) {
        $decoded_data = requestAPI($access_token, "https://api.spotify.com/v1/artists/$artist_id");
        return getArtist($decoded_data);
    }

    function getArtistBySearch($access_token, $searched_artist) {
        $search = strtolower(str_replace(" ", "+", $searched_artist));
        $decoded_data = requestAPI($access_token, "https://api.spotify.com/v1/search?type=artist&q=$search");
        // if size > 0
        $tmp = 0;
        $found_artist = Object;
        foreach ($decoded_data->artists->items as $artist) {
            if (strcmp(strtolower($artist->name), strtolower($searched_artist)) === 0) {
                return (getArtist($artist));
            }
            if (abs(strcmp(strtolower($artist->name), strtolower($searched_artist))) < $tmp || $found_artist === Object) {
                $tmp = abs(strcmp(strtolower($artist->name), strtolower($searched_artist)));
                $found_artist = $artist;
            }
        }
        return getArtist($found_artist);
    }
?>