<?php
    namespace App\Controller;

    use App\Entity\RequestAPI;
    use App\Repository\ActionRepository;
    use App\Repository\AutomationActionRepository;
    use App\Repository\ServiceRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;

    class AutomationActionController extends AbstractController
    {
        private RequestAPI $request_api;
        /**
         * @Route("/automation/action/check", name="automation_action_check")
         */
        public function check(ActionRepository $action_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository)
        {// check si une des erreurs de requete est un bad token pour le refresh
            $old_parameters = array();
            while (true) {
                foreach ($automation_action_repository->findAll() as $automation_action) {
                    // Get needed values
                    $automation_action_id = $automation_action->getId();
                    $action_id = $automation_action->getActionId();
                    $action = $action_repository->find($action_id);
                    if (empty($action)) {
                        continue;
                    }
                    if (strcmp($action->getType(), "action") !== 0) {
                        continue;
                    }
                    $service_id = $action->getServiceId();
                    $service = $service_repository->find($service_id);
                    if (empty($service)) {
                        continue;
                    }
                    $url = "http://localhost:8000/".$service->getName()."/".$action->getType()."/".$action->getIdentifier();
                    // Request to get parameters of the action
                    $parameters = json_decode($this->sendRequest($url."/get_parameters", array("automation_action_id" => $automation_action_id)));
                    if (isset($parameters->code)) {
                        continue;
                    }
                    // Stock the old parameters of the action
                    if (empty($old_parameters[$automation_action_id])) {
                        $old_parameters[$automation_action_id] = $parameters;
                        continue;
                    }
                    // Request to check if the action is validate
                    $response = $this->sendRequest($url, array("new" => $parameters, "old" => $old_parameters[$automation_action_id]));
                    if (isset(json_decode($response)->code)) {
                        continue;
                    }
                    $old_parameters[$automation_action_id] = $parameters;
                    // Trigger all linked reactions
                    if (json_decode($response)->message === true) {
                        $parameters = array("automation_action_id" => $automation_action_id);
                        $response = $this->sendRequest("http://localhost:8000/automation/reaction/trigger", $parameters);
                        if (isset(json_decode($response)->code)) {
                            continue;
                        }
                    }
                }
                sleep(60);
            }
        }
        /**
         * @Route("/automation/reaction/trigger", name="automation_reaction_trigger")
         */
        public function triggerReaction(Request $request, ActionRepository $action_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            if (empty($automation_action_repository->find($automation_action_id))) {
                return new JsonResponse(array("message" => "AutomationAction: automation_action not found"), 404);
            }
            $automation_action = $automation_action_repository->find($automation_action_id);
            $automation_id = $automation_action->getAutomationId();
            if (empty($automation_action_repository->findAutomationReactions($automation_id))) {
                return new JsonResponse(array("message" => "AutomationAction: No reaction found"), 404);
            }
            $automation_reactions = $automation_action_repository->findAutomationReactions($automation_id);
            foreach ($automation_reactions as $automation_reaction) {
                $url = "http://localhost:8000/automation/reaction/do";
                $parameters = array("automation_action_id" => $automation_reaction->getId());
                $response = $this->sendRequest($url, $parameters);
                if (isset(json_decode($response)->code)) {
                    return new JsonResponse(array("message" => json_decode($response)->message), json_decode($response)->code);
                }
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }
        /**
         * @Route("/automation/reaction/do", name="automation_reaction_do")
         */
        public function doReaction(Request $request, ActionRepository $action_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            if (empty($automation_action_repository->find($automation_action_id))) {
                return new JsonResponse(array("message" => "AutomationAction: automation_action not found"), 404);
            }
            $automation_action = $automation_action_repository->find($automation_action_id);
            $action_id = $automation_action->getId();
            if (empty($action_repository->find($action_id))) {
                return new JsonResponse(array("message" => "AutomationAction: Action not found"), 404);
            }
            $action = $action_repository->find($action_id);
            $service_id = $action->getServiceId();
            if (empty($service_repository->find($service_id))) {
                return new JsonResponse(array("message" => "AutomationAction: Service not found"), 404);
            }
            $service = $service_repository->find($service_id);
            $url = "http://localhost:8000/".$service->getName()."/".$action->getType()."/".$action->getIdentifier();
            // Post request to the action url
            $parameters = array("automation_action_id" => $automation_action_id);
            $response = $this->sendRequest($url, $parameters);
            if (isset(json_decode($response)->code)) {
                return new JsonResponse(array("message" => json_decode($response)->message), json_decode($response)->code);
            }
            return new JsonResponse($response, 200);
        }
        private function sendRequest($url, $parameters)
        {
            if (empty($this->request_api)) {
                $this->request_api = new RequestAPI();
            }
            $response = $this->request_api->sendRoute($url, $parameters);
            return $response;
        }
    }
?>