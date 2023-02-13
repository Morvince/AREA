<?php
    namespace App\Entity;

    use App\Repository\ActionRepository;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity(repositoryClass=ActionRepository::class)
     */
    class Action
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
        private $service_id;

        /**
         * @ORM\Column(type="string", length=255)
         */
        private $name;

        /**
         * @ORM\Column(type="string", length=20)
         */
        private $type;

        /**
         * @ORM\Column(type="string", length=20)
         */
        private $identifier;

        /**
         * @ORM\Column(type="json")
         */
        private $fields;

        // Getter
        public function getId()
        {
            return $this->id;
        }
        public function getServiceId()
        {
            return $this->service_id;
        }
        public function getName()
        {
            return $this->name;
        }
        public function getType()
        {
            return $this->type;
        }
        public function getIdentifier()
        {
            return $this->identifier;
        }
        public function getFields()
        {
            return json_decode($this->fields);
        }

        // Setter
        public function setServiceId(int $service_id)
        {
            $this->service_id = $service_id;
            return $this;
        }
        public function setName(string $name)
        {
            $this->name = $name;
            return $this;
        }
        public function setType(string $type)
        {
            $this->type = $type;
            return $this;
        }
        public function setIdentifier(string $identifier)
        {
            $this->identifier = $identifier;
            return $this;
        }
        public function setFields(string $fields)
        {
            $this->fields = $fields;
            return $this;
        }
    }
?>