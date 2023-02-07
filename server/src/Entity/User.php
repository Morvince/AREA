<?php
    namespace App\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity(repositoryClass=UserRepository::class)
     * @ORM\Table(name="`user`")
     */
    class User
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        private $id;

        /**
         * @ORM\Column(type="string", length=255)
         */
        private $username;

        /**
         * @ORM\Column(type="string", length=255)
         */
        private $email;

        /**
         * @ORM\Column(type="string", length=16)
         */
        private $password;

        /**
         * @ORM\Column(type="string", length=64)
         */
        private $token;

        // Getter
        public function getId()
        {
            return $this->id;
        }
        public function getUsername()
        {
            return $this->username;
        }
        public function getEmail()
        {
            return $this->email;
        }
        public function getPassword()
        {
            return $this->password;
        }
        public function getToken()
        {
            return $this->token;
        }

        // Setter
        public function setUsername(string $username)
        {
            $this->username = $username;
            return $this;
        }
        public function setEmail(string $email)
        {
            $this->email = $email;
            return $this;
        }
        public function setPassword(string $password)
        {
            $this->password = $password;
            return $this;
        }
        public function setToken(string $token)
        {
            $this->token = $token;
            return $this;
        }
    }
?>