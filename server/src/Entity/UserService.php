<?php
    namespace App\Entity;

    use App\Repository\UserServiceRepository;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity(repositoryClass=UserServiceRepository::class)
     */
    class UserService
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        private $id;

        /**
         * @ORM\Column(type="integer")
         */
        private $user_id;

        /**
         * @ORM\Column(type="integer")
         */
        private $service_id;

        /**
         * @ORM\Column(type="string", length=512)
         */
        private $access_token;

        /**
         * @ORM\Column(type="string", length=255)
         */
        private $refresh_token;

        // Getter
        public function getId()
        {
            return $this->id;
        }
        public function getUserId()
        {
            return $this->user_id;
        }
        public function getServiceId()
        {
            return $this->service_id;
        }
        public function getAccessToken()
        {
            return $this->access_token;
        }
        public function getRefreshToken()
        {
            return $this->refresh_token;
        }

        // Setter
        public function setUserId(int $user_id)
        {
            $this->user_id = $user_id;
            return $this;
        }
        public function setServiceId(int $service_id)
        {
            $this->service_id = $service_id;
            return $this;
        }
        public function setAccessToken(string $access_token)
        {
            $this->access_token = $access_token;
            return $this;
        }
        public function setRefreshToken(string $refresh_token)
        {
            $this->refresh_token = $refresh_token;
            return $this;
        }
    }
?>