<?php
    class Artist {
        private $external_urls;
        private $followers;
        private $genres;
        private $href;
        private $id;
        private $images;
        private $name;
        private $popularity;
        private $uri;

        public function __construct($external_urls, $followers, $genres, $href, $id, $images, $name, $popularity, $uri) {
            $this->external_urls = $external_urls;
            $this->followers = $followers;
            $this->genres = $genres;
            $this->href = $href;
            $this->id = $id;
            $this->images = $images;
            $this->name = $name;
            $this->popularity = $popularity;
            $this->uri = $uri;
        }

        // Getter
        public function getSpotifyUrl() {
            return $this->external_urls->spotify;
        }
        public function getFollowersNumber() {
            return $this->followers;
        }
        public function getGenres() {
            return $this->genres;
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
        public function getPopularity() {
            return $this->popularity;
        }
        public function getType() {
            return "artist";
        }
        public function getUri() {
            return $this->uri;
        }
    }
?>