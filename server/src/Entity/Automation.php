<?php
    namespace App\Entity;

    use App\Repository\AutomationRepository;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity(repositoryClass=AutomationRepository::class)
     */
    class Automation
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

        // Getter
        public function getId()
        {
            return $this->id;
        }
        public function getUserId()
        {
            return $this->user_id;
        }

        // Setter
        public function setUserId(int $user_id)
        {
            $this->user_id = $user_id;
            return $this;
        }
    }
?>