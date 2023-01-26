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
        public function addAutomation(AutomationRepository $automation_repository)
        {
            $automation = new Automation();
            $automation->setUserId($_SESSION["user_id"]);
            $automation_repository->add($automation, true);
            return new JsonResponse(array("automation_id" => $automation->getId()));
        }
        /**
         * @Route("/automation/get", name="automation_get")
         */
        public function getAutomation(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository)
        {
            if (empty($request->query->get("automation_id"))) {
                return new JsonResponse(array("status" => "error"));// manque le champs automation_id
            }
            $automation_id = $request->query->get("automation_id");
            if (empty($automation_repository->findById($automation_id))) {
                return new JsonResponse(array("status" => "error"));// id inexistante
            }
            $automation = $automation_action_repository->findByAutomationId($automation_id);
            if (empty($automation)) {
                return new JsonResponse(array("status" => "ok"));
            }
            return new JsonResponse(array("status" => "ok"));
        }
    }
?>