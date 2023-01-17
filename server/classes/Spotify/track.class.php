<?php
    class Track {
        private $album_id;
        private $artists;
        private $available_markets;
        private $duration_ms;
        private $explicit;
        private $external_urls;
        private $href;
        private $id;
        private $is_playable;
        private $name;
        private $popularity;
        private $preview_url;
        private $track_number;
        private $type;

        public function __construct($album_id, $artists, $available_markets, $duration_ms, $explicit, $external_urls, $href, $id, $is_playable, $name, $popularity, $preview_url, $track_number, $type) {
            $this->album_id = $album_id;
            $this->artists = $artists;
            $this->available_markets = $available_markets;
            $this->duration_ms = $duration_ms;
            $this->explicit = $explicit;
            $this->external_urls = $external_urls;
            $this->href = $href;
            $this->id = $id;
            $this->is_playable = $is_playable;
            $this->name = $name;
            $this->popularity = $popularity;
            $this->preview_url = $preview_url;
            $this->track_number = $track_number;
            $this->type = $type;
        }

        // Getter
        public function getAlbumId() {
            return $this->album_id;
        }
        public function getArtists() {
            return $this->artists;
        }
        public function getAvailableMarkets() {
            return $this->available_markets;
        }
        public function getMsDuration() {
            return $this->duration_ms;
        }
        public function isExplicit() {
            return $this->explicit;
        }
        public function getSpotifyUrl() {
            return $this->external_urls->spotify;
        }
        public function getApiUrl() {
            return $this->href;
        }
        public function getId() {
            return $this->id;
        }
        public function isPlayable() {
            return $this->is_playable;
        }
        public function getName() {
            return $this->name;
        }
        public function getPopularity() {
            return $this->popularity;
        }
        public function getPreviewUrl() {
            return $this->preview_url;
        }
        public function getTrackNumber() {
            return $this->track_number;
        }
        public function getType() {
            return $this->type;
        }
    }
?>