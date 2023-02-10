<?php
    namespace App\Controller;

    use App\Entity\RequestAPI;
    use App\Repository\ActionRepository;
    use App\Repository\AutomationRepository;
    use App\Repository\AutomationActionRepository;
    use App\Repository\ServiceRepository;
    use App\Repository\UserServiceRepository;
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
        public function check(ActionRepository $action_repository, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        { // check si une des erreurs de requete est un bad token pour le refresh
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
                    $url = "http://localhost:8000/" . $service->getName() . "/" . $action->getType() . "/" . $action->getIdentifier();
                    // Request to get parameters of the action
                    $parameters = $this->sendRequest($url . "/get_parameters", array("automation_action_id" => $automation_action_id));
                    if (isset($parameters->code)) {
                        if (str_contains($parameters->message, "Bad or expired token")) {
                            $this->refreshAccessToken($automation_action, $service, $automation_repository, $user_service_repository);
                        }
                        continue;
                    }
                    // Stock the old parameters of the action
                    if (empty($old_parameters[$automation_action_id])) {
                        $old_parameters[$automation_action_id] = $parameters;
                        continue;
                    }
                    // Request to check if the action is validate
                    $response = $this->sendRequest($url, array("new" => $parameters, "old" => $old_parameters[$automation_action_id]));
                    if (isset($response->code)) {
                        if (str_contains($response->message, "Bad token or expired")) {
                            $this->refreshAccessToken($automation_action, $service, $automation_repository, $user_service_repository);
                        }
                        continue;
                    }
                    $old_parameters[$automation_action_id] = $parameters;
                    // Trigger all linked reactions
                    if ($response->message === true) {
                        $parameters = array("automation_action_id" => $automation_action_id);
                        $response = $this->sendRequest("http://localhost:8000/automation/reaction/trigger", $parameters);
                        if (isset($response->code)) {
                            continue;
                        }
                    }
                }
                sleep(60);
            }
        }
        private function refreshAccessToken($automation_action, $service, $automation_repository, $user_service_repository)
        {
            $automation_id = $automation_action->getAutomationId();
            $automation = $automation_repository->find($automation_id);
            if (empty($automation)) {
                return;
            }
            $response = $this->sendRequest("http://localhost:8000/" . $service->getName() . "/refresh_access_token", array("user_id" => $automation->getUserId()));
            if (isset(json_decode($response)->code)) {
                if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                    return;
                }
                $user_service = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId());
                $user_service_repository->remove($user_service);
            }
            return;
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
                return new JsonResponse($response);
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
            $url = "http://localhost:8000/" . $service->getName() . "/" . $action->getType() . "/" . $action->getIdentifier();
            // Post request to the action url
            $parameters = array("automation_action_id" => $automation_action_id);
            $response = $this->sendRequest($url, $parameters);
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse($response, 200);
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
