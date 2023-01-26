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
                return new JsonResponse(array("status" => "error"));// automation inexistante
            }
            $automation_actions = $automation_action_repository->findByAutomationId($automation_id);
            // formatter une array avec automation in $response
            if (empty($automation_actions)) {
                return new JsonResponse(array("status" => "ok"));
            }
            return new JsonResponse(array("status" => "ok"));
        }
        /**
         * @Route("/automation/edit", name="automation_edit")
         */
        public function editAutomation(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository)
        {
            $data = json_decode($request->getContent(), true);
            if (empty($data->automation_id)) {
                return new JsonResponse(array("status" => "error"));// manque le champs automation_id
            }
            $automation_id = $data->automation_id;
            if (empty($automation_repository->findById($automation_id))) {
                return new JsonResponse(array("status" => "error"));// id inexistante
            }
            $automation_actions = $automation_action_repository->findByAutomationId($automation_id);
            foreach ($automation_actions as $item) {
                $automation_action_repository->remove($item);
            }
            if (empty($data->actions)) {
                return new JsonResponse(array("status" => "ok"));
            }
            $actions = $data->actions;
            foreach ($actions as $action) {
                $automation_action = new AutomationAction();
                $automation_action->setActionId($action->id);
                $automation_action->setAutomationId($automation_id);
                $automation_action->setNumber($action->number);
                $automation_action->setInformations($action->informations);
                $automation_action_repository->add($automation_action, true);
            }
            return new JsonResponse(array("status" => "ok"));
        }
    }
?>