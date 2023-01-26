<?php
    namespace App\Controller;

    use App\Repository\ActionRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;

    class ActionController extends AbstractController
    {
        /**
         * @Route("/action/get", name="action_get")
         */
        public function getAction(Request $request, ActionRepository $action_repository)
        {
            if (empty($request->query->get("action_id"))) {
                return new JsonResponse(array("status" => "error"));//action_id vide dans la requete
            }
            $action_id = $request->query->get("action_id");
            $action = $action_repository->findById($action_id);
            if (empty($action)) {
                return new JsonResponse(array("status" => "error"));//action inexistante
            }
            $action = $action[0];
            //formatter les infos
            return new JsonResponse(array("status" => "ok"));
        }
    }
?>