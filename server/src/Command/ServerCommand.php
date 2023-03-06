<?php
    namespace App\Command;

    use App\Entity\RequestAPI;
    use App\Repository\ActionRepository;
    use App\Repository\AutomationRepository;
    use App\Repository\AutomationActionRepository;
    use App\Repository\ServiceRepository;
    use App\Repository\UserServiceRepository;
    use Symfony\Component\Form\Test\FormIntegrationTestCase;
    use Symfony\Component\Process\Process;
    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Input\InputArgument;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Input\InputOption;
    use Symfony\Component\Console\Output\OutputInterface;
    use Symfony\Component\Console\Style\SymfonyStyle;

    class ServerCommand extends Command
    {
        protected static $defaultName = "app:server";
        protected static $defaultDescription = "Server loop";
        private ActionRepository $action_repository;
        private AutomationRepository $automation_repository;
        private AutomationActionRepository $automation_action_repository;
        private ServiceRepository $service_repository;
        private UserServiceRepository $user_service_repository;

        public function __construct(ActionRepository $action_repository, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            $this->action_repository = $action_repository;
            $this->automation_repository = $automation_repository;
            $this->automation_action_repository = $automation_action_repository;
            $this->service_repository = $service_repository;
            $this->user_service_repository = $user_service_repository;
            parent::__construct();
        }

        protected function configure(): void {}

        protected function execute(InputInterface $input, OutputInterface $output): int
        {
            $io = new SymfonyStyle($input, $output);
            $io->success("Server started");
            $old_parameters = array();
            while (true) {
                foreach ($this->automation_action_repository->findAll() as $automation_action) {
                    // Get needed values
                    $automation_action_id = $automation_action->getId();
                    $action_id = $automation_action->getActionId();
                    $action = $this->action_repository->find($action_id);
                    if (empty($action)) {
                        $io->warning("Action not found with action.id=$action_id in automation_action.id=$automation_action_id");
                        continue;
                    }
                    if (strcmp($action->getType(), "action") !== 0) {
                        continue;
                    }
                    $service_id = $action->getServiceId();
                    $service = $this->service_repository->find($service_id);
                    if (empty($service)) {
                        $io->warning("Service not found with service.id=$service_id in action.id=$action_id");
                        continue;
                    }
                    $url = "server/" . $service->getName() . "/" . $action->getType() . "/" . $action->getIdentifier();
                    // Request to get parameters of the action
                    $parameters = $this->sendRequest($url . "/get_parameters", array("automation_action_id" => $automation_action_id));
                    if (isset($parameters->code)) {
                        try {
                            $io->warning("Parameters error with message=$parameters->message");
                        } catch (\Throwable $th) {
                            $io->warning("Parameters error with message=");
                            print_r($parameters->message);
                        }
                        continue;
                    }
                    // Stock the old parameters of the action
                    if (empty($old_parameters[$automation_action_id])) {
                        $old_parameters[$automation_action_id] = $parameters;
                        $io->success("Fill old_parameters for automation_action.id=$automation_action_id");
                        continue;
                    }
                    // Request to check if the action is validate
                    $response = $this->sendRequest($url, array("automation_action_id" => $automation_action_id, "new" => $parameters, "old" => $old_parameters[$automation_action_id]));
                    if (isset($response->code)) {
                        try {
                            $io->warning("Action error with message=$response->message");
                        } catch (\Throwable $th) {
                            $io->warning("Action error with message=");
                            print_r($response->message);
                        }
                        continue;
                    }
                    $old_parameters[$automation_action_id] = $parameters;
                    // Trigger all linked reactions
                    if ($response->message === true) {
                        $parameters = array("automation_action_id" => $automation_action_id);
                        $response = $this->sendRequest("server/automation/reaction/trigger", $parameters);
                        if (isset($response->code)) {
                            try {
                                $io->warning("Trigger error for the automation_action.id=$automation_action_id with message=$response->message");
                            } catch (\Throwable $th) {
                                $io->warning("Trigger error for the automation_action.id=$automation_action_id with message=");
                                print_r($response->message);
                            }
                            continue;
                        }
                        $io->success("Triggered succes for the automation.id=".$automation_action->getAutomationId());
                    }
                }
                sleep(10);
            }
            return Command::SUCCESS;
        }
        private function refreshAccessToken($automation_action, $service, $automation_repository)
        {
            $automation_id = $automation_action->getAutomationId();
            $automation = $automation_repository->find($automation_id);
            if (empty($automation)) {
                return (array("refreshed" => false, "message" => "Access token not refreshed: automation not found with automation.id=$automation_id in automation_action.id=".$automation_action->getId()));
            }
            $response = $this->sendRequest("server/" . $service->getName() . "/refresh_access_token", array("user_id" => $automation->getUserId()));
            if (isset($response->code)) {
                return (array("refreshed" => false, "message" => "Access token not refreshed for the user.id=".$automation->getUserId()." with the service.id=".$service->getId()." with message:$response->message"));
            }
            return (array("refreshed" => true, "message" => "Access token refreshed for the user.id=".$automation->getUserId()." with the service.id=".$service->getId()));
        }
        private function sendRequest($url, $parameters)
        {
            $request_api = new RequestAPI();
            $response = $request_api->sendRoute($url, $parameters);
            return json_decode($response);
        }
    }
?>