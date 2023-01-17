<?php
    require_once("requester.php");
    require_once("artistService.php");
    require_once("classes/Spotify/track.class.php");

    function getTrack($access_token, $decoded_data) {
        $artists = array();
        foreach ($decoded_data->artists as $artist) {
            array_push($artists, getArtist($access_token, $artist));
        }
        $track = new Track( $decoded_data->album->id,
                            $artists,
                            $decoded_data->available_markets,
                            $decoded_data->duration_ms,
                            $decoded_data->explicit,
                            $decoded_data->external_urls,
                            $decoded_data->href,
                            $decoded_data->id,
                            $decoded_data->is_playable,
                            $decoded_data->name,
                            $decoded_data->popularity,
                            $decoded_data->preview_url,
                            $decoded_data->track_number,
                            $decoded_data->type
                        );
        return $track;
    }

    function getTrackById($access_token, $track_id) {
        $decoded_data = requestAPI($access_token, "https://api.spotify.com/v1/tracks/$track_id");
        return getTrack($access_token, $decoded_data);
    }

    function getTrackBySearch($access_token, $searched_track) {
        $search = strtolower(str_replace(" ", "+", $searched_track));
        $decoded_data = requestAPI($access_token, "https://api.spotify.com/v1/search?type=track&q=$search");
        // if size > 0
        $tmp = 0;
        $found_track = Object;
        foreach ($decoded_data->tracks->items as $track) {
            if (strcmp(strtolower($track->name), strtolower($searched_track)) === 0) {
                return (getTrack($access_token, $track));
            }
            if (abs(strcmp(strtolower($track->name), strtolower($searched_track))) < $tmp || $found_track === Object) {
                $tmp = abs(strcmp(strtolower($track->name), strtolower($searched_track)));
                $found_track = $track;
            }
        }
        return getTrack($access_token, $found_track);
    }
?>