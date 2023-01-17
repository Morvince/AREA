<?php
    class Album {
        private $artists;
        private $available_markets;
        private $external_urls;
        private $href;
        private $id;
        private $images;
        private $name;
        private $release_date;
        private $total_tracks;
        private $type;
        private $tracks;
        private $uri;

        public function __construct($artists, $available_markets, $external_urls, $href, $id, $images, $name, $release_date, $total_tracks, $type, $tracks, $uri) {
            $this->artists = $artists;
            $this->available_markets = $available_markets;
            $this->external_urls = $external_urls;
            $this->href = $href;
            $this->id = $id;
            $this->images = $images;
            $this->name = $name;
            $this->release_date = $release_date;
            $this->total_tracks = $total_tracks;
            $this->type = $type;
            $this->tracks = $tracks;
            $this->uri = $uri;
        }

        // Getter
        public function getArtists() {
            return $this->artists;
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
        public function getImages() {
            return $this->images;
        }
        public function getName() {
            return $this->name;
        }
        public function getReleaseDate() {
            return $this->release_date;
        }
        public function getTrackNumber() {
            return $this->total_tracks;
        }
        public function getType() {
            return $this->type;
        }
        public function getTracks() {
            return $this->tracks;
        }
        public function getUri() {
            return $this->uri;
        }
    }
?>