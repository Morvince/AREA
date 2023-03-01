<?php
    namespace App\Controller;

    use App\Entity\Automation;
    use App\Entity\AutomationAction;
    use App\Entity\RequestAPI;
    use App\Repository\AutomationRepository;
    use App\Repository\ActionRepository;
    use App\Repository\UserRepository;
    use App\Repository\ServiceRepository;
    use App\Repository\AutomationActionRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;

    class AutomationController extends AbstractController
    {
        private RequestAPI $request_api;

        /**
         * @Route("/automation/add", name="automation_add")
         */
        public function addAutomation(Request $request, UserRepository $user_repository, AutomationRepository $automation_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->token)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Spotify: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user_id = $user->getId();
            // Put a new automation in database
            $automation = new Automation();
            $automation->setUserId($user_id);
            if (!empty($request_content->name)) {
                $automation->setName($request_content->name);
            }
            $automation_repository->add($automation, true);
            if (!empty($request_content->actions)) {
                $response = $this->sendRequest("http://localhost/automation/edit", array("automation_id" => $automation->getId(), "actions" => $request_content->actions));
                if (isset($response->code)) {
                    return new JsonResponse(array("message" => $response->message), $response->code);
                }
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }
        /**
         * @Route("/automation/get", name="automation_get")
         */
        public function getAutomation(Request $request, UserRepository $user_repository, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ActionRepository $action_repository, ServiceRepository $service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->token)) {
                return new JsonResponse(array("message" => "Automation: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Automation: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            if (empty($automation_repository->findByUserId($user->getId()))) {
                return new JsonResponse(array("automations" => array()), 200);
            }
            $automations = $automation_repository->findByUserId($user->getId());
            $formatted = array();
            foreach ($automations as $automation) {
                $automation_actions = $automation_action_repository->findByAutomationId($automation->getId());
                $tmp_automation_actions = array();
                foreach ($automation_actions as $automation_action) {
                    if (empty($action_repository->find($automation_action->getActionId()))) {
                        return new JsonResponse(array("message" => "Automation: Missing action"), 400);
                    }
                    $action = $action_repository->find($automation_action->getActionId());
                    if (empty($service_repository->find($action->getServiceId()))) {
                        return new JsonResponse(array("message" => "Automation: Missing service"), 400);
                    }
                    $service = $service_repository->find($action->getServiceId());
                    array_push($tmp_automation_actions, array("id" => $action->getId(), "name" => $action->getName(), "service" => $service->getName(), "type" => $action->getType(), "number" => $automation_action->getNumber(), "fields" => $action->getFields(), "values" => $automation_action->getInformations()));
                }
                $tmp_automation = array("name" => $automation->getName(), "id" => $automation->getId(), "automation_actions" => $tmp_automation_actions);
                array_push($formatted, $tmp_automation);
            }
            return new JsonResponse(array("automations" => $formatted), 200);
        }
        /**
         * @Route("/automation/edit", name="automation_edit")
         */
        public function editAutomation(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_id)) {
                return new JsonResponse(array("message" => "Automation: Missing field"), 400);
            }
            $automation_id = $request_content->automation_id;
            if (empty($automation_repository->findById($automation_id))) {
                return new JsonResponse(array("message" => "Automation: Automation not found"), 404);
            }
            $automation_actions = $automation_action_repository->findByAutomationId($automation_id);
            // Remove all actions from the automation in database
            foreach ($automation_actions as $item) {
                $automation_action_repository->remove($item);
            }
            if (empty($request_content->actions)) {
                return new JsonResponse(array("message" => "OK"), 200);
            }
            // Put datas in database
            $actions = $request_content->actions;
            foreach ($actions as $action) {
                $automation_action = new AutomationAction();
                $automation_action->setActionId($action->id);
                $automation_action->setAutomationId($automation_id);
                $automation_action->setNumber($action->number);
                $automation_action->setInformations(json_encode($action->informations));
                $automation_action_repository->add($automation_action, true);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }
        /**
         * @Route("/automation/delete", name="automation_delete")
         */
        public function deleteAutomation(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_id)) {
                return new JsonResponse(array("message" => "Automation: Missing field"), 400);
            }
            $automation_id = $request_content->automation_id;
            if (empty($automation_repository->findById($automation_id))) {
                return new JsonResponse(array("message" => "Automation: Automation not found"), 404);
            }
            $automation_actions = $automation_action_repository->findByAutomationId($automation_id);
            // Remove all actions from the automation in database
            foreach ($automation_actions as $item) {
                $automation_action_repository->remove($item);
            }
            $automation_repository->remove($automation_repository->find($automation_id));
            return new JsonResponse(array("message" => "OK"), 200);
        }

        private function sendRequest($url, $parameters)
        {
            if (empty($this->request_api)) {
                $this->request_api = new RequestAPI();
            }
            $response = $this->request_api->sendRoute($url, $parameters);
            return json_decode($response);
        }
    }
?>