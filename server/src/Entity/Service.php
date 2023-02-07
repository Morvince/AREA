<?php
    namespace App\Entity;

    use App\Repository\ServiceRepository;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity(repositoryClass=ServiceRepository::class)
     */
    class Service
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
        private $name;

        /**
         * @ORM\Column(type="string", length=255)
         */
        private $identifiers;

        // Getter
        public function getId()
        {
            return $this->id;
        }
        public function getName()
        {
            return $this->name;
        }
        public function getIdentifiers()
        {
            return $this->identifiers;
        }

        // Setter
        public function setIdentifiers(string $identifiers)
        {
            $this->identifiers = $identifiers;
            return $this;
        }
    }
?>