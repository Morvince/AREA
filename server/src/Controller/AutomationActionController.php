<?php
    namespace App\Controller;

    use App\Repository\ActionRepository;
    use App\Repository\AutomationActionRepository;
    use App\Repository\ServiceRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;

    class AutomationActionController extends AbstractController
    {
        /**
         * @Route("/automation/action/do", name="automation_action_do")
         */
        public function doAction(Request $request, ActionRepository $action_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository)
        {
            // Get needed values
            if (empty($request->query->get("automation_action_id"))) {
                return new JsonResponse(array("message" => "AutomationAction: Missing field"), 400);
            }
            $automation_action_id = $request->query->get("automation_action_id");
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
            $headers = array(
                "Accept: application/json",
                "Content-Type: application/json",
            );
            $parameters = array("automation_action_id" => $automation_action_id);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
            $response = curl_exec($ch);
            curl_close($ch);
            if (isset(json_decode($response)->code)) {
                return new JsonResponse(array(json_decode($response)->message), json_decode($response)->code);
            }
            return new JsonResponse($response, 200);
        }
    }
?>