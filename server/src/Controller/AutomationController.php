<?php
    namespace App\Controller;

    use App\Entity\Automation;
    use App\Entity\AutomationAction;
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
            // Put a new automation in database
            $automation = new Automation();
            $automation->setUserId($_SESSION["user_id"]);
            $automation_repository->add($automation, true);
            return new JsonResponse(array("automation_id" => $automation->getId()), 200);
        }
        /**
         * @Route("/automation/get", name="automation_get")
         */
        public function getAutomation(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository)
        {
            // Get needed values
            if (empty($request->query->get("automation_id"))) {
                return new JsonResponse(array("message" => "Automation: Missing field"), 400);
            }
            $automation_id = $request->query->get("automation_id");
            if (empty($automation_repository->findById($automation_id))) {
                return new JsonResponse(array("message" => "Automation: Automation not found"), 404);
            }
            $automation_actions = $automation_action_repository->findByAutomationId($automation_id);
            if (empty($automation_actions)) {
                return new JsonResponse(array(), 200);
            }
            // formatter une array avec automation in $response
            return new JsonResponse($automation_actions, 200);
        }
        /**
         * @Route("/automation/edit", name="automation_edit")
         */
        public function editAutomation(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository)
        {
            // Get needed values
            $data = json_decode($request->getContent(), true);
            if (empty($data->automation_id)) {
                return new JsonResponse(array("message" => "Automation: Missing field"), 400);
            }
            $automation_id = $data->automation_id;
            if (empty($automation_repository->findById($automation_id))) {
                return new JsonResponse(array("message" => "Automation: Automation not found"), 404);
            }
            $automation_actions = $automation_action_repository->findByAutomationId($automation_id);
            // Remove all actions from the automation in database
            foreach ($automation_actions as $item) {
                $automation_action_repository->remove($item);
            }
            if (empty($data->actions)) {
                return new JsonResponse(array("message" => "OK"), 200);
            }
            // Put datas in database
            $actions = $data->actions;
            foreach ($actions as $action) {
                $automation_action = new AutomationAction();
                $automation_action->setActionId($action->id);
                $automation_action->setAutomationId($automation_id);
                $automation_action->setNumber($action->number);
                $automation_action->setInformations($action->informations);
                $automation_action_repository->add($automation_action, true);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }
    }
?>