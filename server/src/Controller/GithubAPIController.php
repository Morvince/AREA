<?php
    namespace App\Controller;

    use App\Entity\RequestAPI;
    use App\Entity\UserService;
    use App\Repository\AutomationRepository;
    use App\Repository\AutomationActionRepository;
    use App\Repository\ServiceRepository;
    use App\Repository\UserRepository;
    use App\Repository\UserServiceRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;

    class GithubAPIController extends AbstractController
    {
        private const API_URL = "https://api.github.com/";
        private RequestAPI $request_api;

        /**
         * @Route("/github/connect", name="github_api_connect")
         */
        public function connect(Request $request, ServiceRepository $service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->redirect_uri)) {
                return new JsonResponse(array("message" => "Github: Missing field"), 400);
            }
            $redirect_uri = $request_content->redirect_uri;
            $service = $service_repository->findByName("github");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Github: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (empty($identifiers)) {
                return new JsonResponse(array("message" => "Github: Identifiers error"), 422);
            }
            $client_id = $identifiers[0];
            // Compose the authorization scope
            $scope = array(
                "repo", "admin:repo_hook", "admin:org", "admin:public_key",
                "admin:org_hook", "gist", "notifications", "user", "project",
                "delete_repo", "write:discussion", "read:discussion",
                "write:packages", "read:packages", "delete:packages",
                "admin:gpg_key", "codespace", "workflow", "offline_access"
            );
            $scope = implode(" ", $scope);
            // Set the state when the request is good
            $state = "17";
            // Compose the authorization url
            $authorization_url = "https://github.com/login/oauth/authorize?client_id=$client_id&response_type=code&redirect_uri=$redirect_uri&scope=$scope&state=$state";
            return new JsonResponse(array("authorization_url" => $authorization_url), 200);
        }
        /**
         * @Route("/github/get_access_token", name="github_api_get_access_token")
         */
        public function getAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->state)) {
                return new JsonResponse(array("message" => "Github: Missing field"), 400);
            }
            $state = $request_content->state;
            if ($state != "17") {
                return new JsonResponse(array("message" => "Github: Bad request to get access token"), 400);
            }
            if (empty($request_content->token) || empty($request_content->redirect_uri) || empty($request_content->code)) {
                return new JsonResponse(array("message" => "Github: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Github: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user_id = $user->getId();
            $code = $request_content->code;
            $redirect_uri = $request_content->redirect_uri;
            $service = $service_repository->findByName("github");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Github: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 2) {
                return new JsonResponse(array("message" => "Github: Identifiers error"), 422);
            }
            $client_id = $identifiers[0];
            $client_secret = $identifiers[1];
            // Request for the access token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://github.com/login/oauth/access_token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=$code&redirect_uri=$redirect_uri");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$client_secret");
            $headers = array(
                            "Accept: application/json",
                            "Content-Type: application/x-www-form-urlencoded"
                        );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            curl_close($ch);
            if (!isset(json_decode($result)->access_token)) {
                return new JsonResponse(array("message" => "Github: Bad code to get access token"), 400);
            }
            // Put or edit datas in database
            if (empty($user_service_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                $user_service = new UserService();
                $user_service->setUserId($user_id);
                $user_service->setServiceId($service->getId());
            } else {
                $user_service = $user_service_repository->findByUserIdAndServiceId($user_id, $service->getId())[0];
            }
            $user_service->setAccessToken(json_decode($result)->access_token);
            $user_service_repository->add($user_service, true);
            return new JsonResponse(array("message" => "OK", 200));
        }
        /**
         * @Route("/github/refresh_access_token", name="github_api_refresh_access_token")
         */
        public function refreshAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->user_id)) {
                return new JsonResponse(array("message" => "Github: Missing field"), 400);
            }
            $user_id = $request_content->user_id;
            $service = $service_repository->findByName("github");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Github: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 2) {
                return new JsonResponse(array("message" => "Github: Identifiers error"), 422);
            }
            if (empty($user_service_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                return new JsonResponse(array("message" => "Github: Refresh token not found"), 404);
            }
            $user_service = $user_service_repository->findByUserIdAndServiceId($user_id, $service->getId())[0];
            if (!empty($user_service)) {
                $user_service_repository->remove($user_service);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }
        /**
         * @Route("/github/connected", name="github_api_connected")
         */
        public function isConnected(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->token)) {
                return new JsonResponse(array("message" => "Github: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Github: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user_id = $user->getId();
            $service = $service_repository->findByName("github");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Github: Service not found"), 404);
            }
            $service = $service[0];
            if (empty($user_service_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                return new JsonResponse(array("connected" => false), 200);
            }
            return new JsonResponse(array("connected" => true), 200);
        }
        private function sendRequest($access_token, $endpoint, $method = "GET", $parameters = array())
        {
            if (empty($this->request_api)) {
                $this->request_api = new RequestAPI();
            }
            $response = $this->request_api->send($access_token, self::API_URL . $endpoint, $method, $parameters, "User-Agent: Area");
            if (isset(json_decode($response)->error)) {
                switch (json_decode($response)->error->status) {
                    case 400:
                        $response = json_encode(array("message" => "Github: Bad request", "code" => 400));
                        break;
                    case 401:
                        $response = json_encode(array("message" => "Github: Bad or expired token", "code" => 401));
                        break;
                    case 403:
                        $response = json_encode(array("message" => "Github: Forbidden", "code" => 403));
                        break;
                    case 404:
                        $response = json_encode(array("message" => "Github: Ressource not found", "code" => 404));
                        break;
                    case 429:
                        $response = json_encode(array("message" => "Github: Too many requests", "code" => 429));
                        break;
                    case 500:
                        $response = json_encode(array("message" => "Github: Internal server error", "code" => 500));
                        break;
                    case 502:
                        $response = json_encode(array("message" => "Github: Bad gateway", "code" => 502));
                        break;
                    case 503:
                        $response = json_encode(array("message" => "Github: Service unavailable", "code" => 503));
                        break;
                    default:
                        break;
                }
            }
            return $response;
        }

        /**
         * @Route("/github/get_user_repos", name="github_api_get_user_repos")
         */
        public function getUserRepos(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->token)) {
                return new JsonResponse(array("message" => "Github: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Github: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user_id = $user->getId();
            $service = $service_repository->findByName("github");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Github: Service not found"), 404);
            }
            $service = $service[0];
            if (empty($user_service_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                return new JsonResponse(array("message" => "Github: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($user_id, $service->getId())[0]->getAccessToken();
            // Request for the user playlists
            $response = json_decode($this->sendRequest($access_token, "user/repos"));
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            $formatted = array();
            foreach ($response->items as $item) {
                array_push($formatted, array("name" => $item->full_name, "id" => $item->id));
            }
            return new JsonResponse(array("items" => $formatted), 200);
        }
        /**
         * @Route("/github/get_repo_branches", name="github_api_get_repos_branches")
         */
        public function getRepoBranches(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->token)) {
                return new JsonResponse(array("message" => "Github: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Github: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user_id = $user->getId();
            $service = $service_repository->findByName("github");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Github: Service not found"), 404);
            }
            $service = $service[0];
            if (empty($user_service_repository->findByUserIdAndServiceId($user_id, $service->getId()))) {
                return new JsonResponse(array("message" => "Github: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($user_id, $service->getId())[0]->getAccessToken();
            if (empty($request_content->repo)) {
                return new JsonResponse(array("message" => array()), 200);
            }
            $repo = $request_content->repo;
            // Request for the branches of the repo
            $response = json_decode($this->sendRequest($access_token, "repos/$repo/branches"));
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            $formatted = array();
            foreach ($response->items as $item) {
                array_push($formatted, array("name" => $item->name, "id" => $item->commit->sha));
            }
            return new JsonResponse(array("items" => $formatted), 200);
        }
        // Action
        /**
         * @Route("/github/action/check_last_commit", name="github_api_check_last_commit")
         */
        public function hasBranchNewCommit(Request $request)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->new) || empty($request_content->old)) {
                return new JsonResponse(array("message" => "Github: Missing field"), 400);
            }
            $old_date = $request_content->old;
            $new_date = $request_content->new;
            // Check if a new commit has been pushed
            if (strtotime($old_date) < strtotime($new_date)) {
                return new JsonResponse(array("message" => true), 200);
            }
            return new JsonResponse(array("message" => false), 200);
        }
        /**
         * @Route("/github/action/check_last_commit/get_parameters", name="github_api_check_last_commit_parameters")
         */
        public function getHasBranchNewCommitParameters(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Github: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Github: automation_action ID not found"), 404);
            }
            $service = $service_repository->findByName("github");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Github: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Github: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            $informations = $automation_action->getInformations();
            if (empty($informations->repo)) {
                return new JsonResponse(array("message" => "Github: No repository URL"), 404);
            }
            // Request to get the last commit
            // $response = json_decode($this->sendRequest($access_token, "repos/Morvince/AREA/commits/102-implement-github-service")); pour specifier une branche
            $response = json_decode($this->sendRequest($access_token, "repos/$informations->repo/commits"));
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            if (empty($response)) {
                return new JsonResponse("2001-03-17 17:16:18", 200);
            }
            return new JsonResponse($response[0]->commit->author->date, 200);
        }

        // Reaction
        /**
         * @Route("/github/reaction/create_issue", name="github_api_reaction_create_issue")
         */
        public function createIssue(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->automation_action_id)) {
                return new JsonResponse(array("message" => "Github: Missing field"), 400);
            }
            $automation_action_id = $request_content->automation_action_id;
            $automation_action = $automation_action_repository->find($automation_action_id);
            if (empty($automation_action)) {
                return new JsonResponse(array("message" => "Github: automation_action ID not found"), 404);
            }
            $service = $service_repository->findByName("github");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Github: Service not found"), 404);
            }
            $service = $service[0];
            $automation = $automation_repository->find($automation_action->getAutomationId());
            if (empty($user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId()))) {
                return new JsonResponse(array("message" => "Github: Access token not found"), 404);
            }
            $access_token = $user_service_repository->findByUserIdAndServiceId($automation->getUserId(), $service->getId())[0]->getAccessToken();
            $informations = $automation_action->getInformations();
            if (explode("/", $informations->repo) !== 2) {
                return new JsonResponse(array("message" => "Github: Bad repository URL"), 404);
            }
            if (empty($informations->title)) {
                return new JsonResponse(array("message" => "Github: An issue need a Title"), 404);
            }
            $parameters = array(
                "owner" => explode("/", $informations->repo)[0],
                "repo" => explode("/", $informations->repo)[1],
                "title" => $informations->title,
                "body" => $informations->body
            );
            // Request to create an issue
            $response = json_decode($this->sendRequest($access_token, "repos/$informations->repo/issues", "POST", $parameters));
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }
        /**
         * @Route("/github/test", name="github_api_test")
         */
        public function test(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
        {
            $service = $service_repository->findByName("github");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Github: Service not found"), 404);
            }
            $service = $service[0];
            $access_token = $user_service_repository->findByUserIdAndServiceId(1, $service->getId())[0]->getAccessToken();
            return new JsonResponse(array("message" => strtotime("2001-03-17 17:16:18")), 200);
        }
    }
?>
