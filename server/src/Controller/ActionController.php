<?php
    namespace App\Controller;

    use App\Repository\ActionRepository;
    use App\Repository\ServiceRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;

    class ActionController extends AbstractController
    {
        /**
         * @Route("/action/get_all", name="action_get_all")
         */
        public function getAllActions(ActionRepository $action_repository, ServiceRepository $service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $actions = $action_repository->findAll();
            if (empty($actions)) {
                return new JsonResponse(array("message" => "Action: No action found"), 404);
            }
            // Formatting the return array
            $formatted = array();
            foreach ($actions as $action) {
                $service = $service_repository->find($action->getServiceId());
                if (empty($service)) {
                    continue;
                }
                array_push($formatted, array("id" => $action->getId(), "name" => $action->getName(), "service" => $service->getName(), "type" => $action->getType()));
            }
            return new JsonResponse(array("actions" => $formatted), 200);
        }
    }
?>