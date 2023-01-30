<?php
    namespace App\Controller;

    use App\Repository\ActionRepository;
    use App\Repository\ServiceRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;

    class ActionController extends AbstractController
    {
        /**
         * @Route("/action/get_all", name="action_get_all")
         */
        public function getAllActions(ActionRepository $action_repository, ServiceRepository $service_repository)
        {
            $actions = $action_repository->findAll();
            if (empty($actions)) {
                return new JsonResponse(array("status" => "error"));//aucune action
            }
            $formatted = array();
            foreach ($actions as $action) {
                $service = $service_repository->findById($action->getServiceId());
                if (empty($service)) {
                    continue;
                } else {
                    $service = $service[0];
                }
                array_push($formatted, array("id" => $action->getId(), "name" => $action->getName(), "service" => $service->getName(), "type" => $action->getType()));
            }
            return new JsonResponse($formatted);
        }
    }
?>