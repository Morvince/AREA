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
         * @Route("/automation/reaction/trigger", name="automation_reaction_trigger")
         */
        public function triggerReaction(Request $request, ActionRepository $action_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository)
        {
            // Get needed values
            // $request_content = json_decode($request->getContent());
            // if (empty($request_content->automation_action_id)) {
            //     return new JsonResponse(array("message" => "Spotify: Missing field"), 400);
            // }
            // $automation_action_id = $request_content->automation_action_id;
            if (empty($request->query->get("automation_action_id"))) {
                return new JsonResponse(array("message" => "AutomationAction: Missing field"), 400);
            }
            $automation_action_id = $request->query->get("automation_action_id");
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