<?php
    namespace App\Entity;

    use App\Repository\AutomationActionRepository;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity(repositoryClass=AutomationActionRepository::class)
     */
    class AutomationAction
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
        private $automation_id;

        /**
         * @ORM\Column(type="integer")
         */
        private $action_id;

        /**
         * @ORM\Column(type="integer")
         */
        private $number;

        /**
         * @ORM\Column(type="string", length=512)
         */
        private $informations;

        // Getter
        public function getId()
        {
            return $this->id;
        }
        public function getAutomationId()
        {
            return $this->automation_id;
        }
        public function getActionId()
        {
            return $this->action_id;
        }
        public function getNumber()
        {
            return $this->number;
        }
        public function getInformations()
        {
            return $this->informations;
        }

        // Setter
        public function setAutomationId(int $automation_id)
        {
            $this->automation_id = $automation_id;
            return $this;
        }
        public function setActionId(int $action_id)
        {
            $this->action_id = $action_id;
            return $this;
        }
        public function setNumber(int $number)
        {
            $this->number = $number;
            return $this;
        }
        public function setInformations(string $informations)
        {
            $this->informations = $informations;
            return $this;
        }
    }
?>