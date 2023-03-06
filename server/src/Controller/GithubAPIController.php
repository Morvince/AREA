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
         * @brief This function is used to connect the service with the user account 
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when the action is working
         */
        
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
         * @brief This function is used to get the access_token with the OAuth request
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when the action is working
         */

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
         * @brief This function is used to set the new refresh token for the user
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when the action is working
         */

        /**
         * @Route("/github/refresh_access_token", name="github_api_refresh_access_token")
         */
        public function refreshAccessToken(Request $request, ServiceRepository $service_repository, UserRepository $user_repository, UserServiceRepository $user_service_repository)
        {
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->access_token)) {
                return new JsonResponse(array("message" => "Github: Missing field"), 400);
            }
            $access_token = $request_content->access_token;
            $service = $service_repository->findByName("github");
            if (empty($service)) {
                return new JsonResponse(array("message" => "Github: Service not found"), 404);
            }
            $service = $service[0];
            $identifiers = explode(";", $service->getIdentifiers());
            if (count($identifiers) != 2) {
                return new JsonResponse(array("message" => "Github: Identifiers error"), 422);
            }
            if (empty($user_service_repository->findBy(array("access_token" => $access_token)))) {
                return new JsonResponse(array("message" => "Github: Refresh token not found"), 404);
            }
            $user_service = $user_service_repository->findBy(array("access_token" => $access_token))[0];
            if (!empty($user_service)) {
                $user_service_repository->remove($user_service, true);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }

        /**
         * @brief This function is used to get if the service is connected and currently working
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when the action is working
         */

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

        /**
         * @brief This function allow to sendRequest to the API with the given parameters
         * 
         * @param[in] access_token when you need to request via an user account
         * @param[in] endpoint to use for your request
         * @param[in] method to precise the method used for the request
         * @param[in] parameters if you need to access on the database parameters for reaction/action
         * @param[in] added_header to modify the request header with others informations
         * @param[in] authorization to precise the different authorization for the request
         * @return JsonReponse will return a Json object containing all the information when working
         */

        private function sendRequest($access_token, $endpoint, $method = "GET", $parameters = array())
        {
            if (empty($this->request_api)) {
                $this->request_api = new RequestAPI();
            }
            $response = json_decode($this->request_api->send($access_token, self::API_URL . $endpoint, $method, $parameters, "User-Agent: Area"));
            if (isset($response->message) && isset($response->documentation_url)) {
                $this->request_api->sendRoute("http://localhost/github/refresh_access_token", array("access_token" => $access_token));
                $response = array("message" => "Github: Help on $response->documentation_url", "code" => 400);
            }
            return json_decode(json_encode($response));
        }

        /**
         * @brief This function is used to get all the user repository on github
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when working
         */

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
            $response = $this->sendRequest($access_token, "user/repos");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            $formatted = array();
            foreach ($response as $item) {
                array_push($formatted, array("name" => $item->full_name, "id" => $item->id));
            }
            return new JsonResponse(array("items" => $formatted), 200);
        }

        /**
         * @brief This function is used to get the repository branche on github
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when working
         */

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
                return new JsonResponse(array("items" => array()), 200);
            }
            $repo = $request_content->repo;
            // Request for the branches of the repo
            $response = $this->sendRequest($access_token, "repos/$repo/branches");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            $formatted = array();
            foreach ($response as $item) {
                array_push($formatted, array("name" => $item->name, "id" => $item->commit->sha));
            }
            return new JsonResponse(array("items" => $formatted), 200);
        }

        // Action

        /**
         * @brief This function is used to check last commit on github
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when working
         */

        /**
         * @Route("/github/action/check_last_commit", name="github_api_action_check_last_commit")
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
         * @brief This function is used to get the parameters for the check commit action
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when working
         */

        /**
         * @Route("/github/action/check_last_commit/get_parameters", name="github_api_action_check_last_commit_parameters")
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
            $response = $this->sendRequest($access_token, "repos/$informations->repo/commits");
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
         * @brief This function is used to create an issue on your github project
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when working
         */

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
            if (count(explode("/", $informations->repo)) !== 2) {
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
            $response = $this->sendRequest($access_token, "repos/$informations->repo/issues", "POST", $parameters);
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }

        /**
         * @brief This function is used to edit the requested readme on repository
         * 
         * @param[in] request used to get the content of the old username and the new one
         * @param[in] automation_repo to access linked values on database
         * @param[in] automation_action_repo to access linked values on database
         * @param[in] service_repo to access linked values on database
         * @param[in] user_service_repo to access linked values on database
         * @return JsonReponse will return a Json object containing all the information when working
         */

        /**
         * @Route("/github/reaction/create_edit_readme", name="github_api_reaction_create_edit_readme")
         */
        public function createEditReadme(Request $request, AutomationRepository $automation_repository, AutomationActionRepository $automation_action_repository, ServiceRepository $service_repository, UserServiceRepository $user_service_repository)
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
            if (count(explode("/", $informations->repo)) !== 2) {
                return new JsonResponse(array("message" => "Github: Bad repository URL"), 404);
            }
            $response = $this->sendRequest($access_token, "repos/$informations->repo/contents");
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            $path = "README.md";
            $found_sha = "";
            foreach ($response as $item) {
                if (strcmp($item->name, $path) === 0) {
                    $found_sha = $item->sha;
                }
            }
            if (!empty($found_sha)) {
                $data = array(
                    "owner" => explode("/", $informations->repo)[0],
                    "repo" => explode("/", $informations->repo)[1],
                    "path" => $path,
                    "message" => "[AREA API] : Updating $path",
                    "content" => base64_encode($informations->content),
                    "sha" => $found_sha
                );
            } else {
                $data = array(
                    "owner" => explode("/", $informations->repo)[0],
                    "repo" => explode("/", $informations->repo)[1],
                    "path" => $path,
                    "message" => "[AREA API] : Creating $path",
                    "content" => base64_encode($informations->content)
                );
            }
            $response = $this->sendRequest($access_token, "repos/$informations->repo/contents/$path", "PUT", $data);
            if (isset($response->code)) {
                return new JsonResponse(array("message" => $response->message), $response->code);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }
    }
?>
