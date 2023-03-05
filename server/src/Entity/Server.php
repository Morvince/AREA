<?php
    namespace App\Entity;

    use App\Repository\ServerRepository;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity(repositoryClass=ServerRepository::class)
     */
    class Server
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        private $id;

        /**
         * @ORM\Column(type="string", length=128)
         */
        private $host;

        /**
         * @ORM\Column(type="string", length=128)
         */
        private $email;

        /**
         * @ORM\Column(type="string", length=64)
         */
        private $password;

        /**
         * @ORM\Column(type="string", length=64)
         */
        private $smtpsecure;

        /**
         * @ORM\Column(type="integer")
         */
        private $port;

        // Getter
        public function getId()
        {
            return $this->id;
        }
        public function getHost()
        {
            return $this->host;
        }
        public function getEmail()
        {
            return $this->email;
        }
        public function getPassword()
        {
            return $this->password;
        }
        public function getSMTPSecure()
        {
            return $this->smtpsecure;
        }
        public function getPort()
        {
            return $this->port;
        }

        // Setter
        public function setHost(string $host)
        {
            $this->host = $host;
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
        public function setSMTPSecure(string $smtpsecure)
        {
            $this->smtpsecure = $smtpsecure;
            return $this;
        }
        public function setPort(int $port)
        {
            $this->port = $port;
            return $this;
        }
    }
?>