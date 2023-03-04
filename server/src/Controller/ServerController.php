<?php
    namespace App\Controller;

    use App\Repository\ActionRepository;
    use App\Repository\ServiceRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    class ServerController extends AbstractController
    {
        /**
         * @Route("/about.json", name="server_about_json")
         */
        public function about(Request $request, ActionRepository $action_repository, ServiceRepository $service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            $project = array("name" => "Hapilink", "subject" => "Create your own automation by linking an action to all reactions you want.");
            $client = array("host" => $request->getClientIp());
            $services = array();
            foreach ($service_repository->findAll() as $service) {
                $actions = $action_repository->findByTypeAndServiceId("action", $service->getId());
                $reactions = $action_repository->findByTypeAndServiceId("reaction", $service->getId());
                $tmp_actions = array();
                $tmp_reactions = array();
                foreach ($actions as $action) {
                    array_push($tmp_actions, array("name" => $action->getIdentifier(), "description" => $action->getName()));
                }
                foreach ($reactions as $reaction) {
                    array_push($tmp_reactions, array("name" => $reaction->getIdentifier(), "description" => $reaction->getName()));
                }
                $tmp_services = array("name" => $service->getName(), "actions" => $tmp_actions, "reactions" => $tmp_reactions);
                array_push($services, $tmp_services);
            }
            $server = array("current_time" => time(), "services" => $services);
            $about = array("project" => $project, "client" => $client, "server" => $server);
            echo "<pre>";
            return new Response(json_encode($about, JSON_PRETTY_PRINT), 200);
        }
    }
?>