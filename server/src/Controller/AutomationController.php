<?php
    namespace App\Controller;

    use App\Entity\Automation;
    use App\Entity\Action;
    use App\Repository\AutomationRepository;
    use App\Repository\AutomationActionRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;

    class AutomationController extends AbstractController
    {
        /**
         * @Route("/automation/add", name="automation_add")
         */
        public function add(AutomationRepository $automation_repository)
        {
            $automation = new Automation();
            $automation->setUserId($_SESSION["user_id"]);
            $automation_repository->add($automation, true);
            return new JsonResponse(array("automation_id" => $automation->getId()));
        }
    }
?>