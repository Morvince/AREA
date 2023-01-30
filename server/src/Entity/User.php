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

        // Getter
        public function getId(): ?int
        {
            return $this->id;
        }
        public function getUsername(): ?string
        {
            return $this->username;
        }
        public function getEmail(): ?string
        {
            return $this->email;
        }
        public function getPassword(): ?string
        {
            return $this->password;
        }

        // Setter
        public function setUsername(string $username): self
        {
            $this->username = $username;
            return $this;
        }
        public function setEmail(string $email): self
        {
            $this->email = $email;
            return $this;
        }
        public function setPassword(string $password): self
        {
            $this->password = $password;
            return $this;
        }
    }
?>