<?php
    namespace App\Controller;
    
    use App\Entity\User;
    use App\Repository\UserRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;

    class UserController extends AbstractController
    {
        /**
         * @Route("/login", name="user_login")
         */
        public function login(Request $request, UserRepository $user_repository)
        {
            // header('Access-Control-Allow-Origin: *');
            $request_content = json_decode($request->getContent());
            if (!empty($request_content->password) && !empty($request_content->login)) {
                // Get needed values
                $login = $request_content->login;
                $password = $request_content->password;
                $password = hash("haval256,5", $password);
                $password = hash("md5", $password);
                $users = $user_repository->findAll();
                // Check datas in database
                foreach ($users as $user) {
                    if (strcmp($user->getPassword(), $password) === 0 &&
                        (strcmp($user->getUsername(), $login) === 0 || strcmp($user->getEmail(), $login) === 0)) {
                        return new JsonResponse(array("token" => $user->getToken()), 200);
                    }
                }
                return new JsonResponse(array("message" => "User: Wrong password"), 401);
            }
            return new JsonResponse(array("message" => "User: Missing field"), 400);
        }

        /**
         * @Route("/register", name="user_register")
         */
        public function register(Request $request, UserRepository $user_repository)
        {
            // header('Access-Control-Allow-Origin: *');
            $request_content = json_decode($request->getContent());
            if (!empty($request_content->username) && !empty($request_content->email) && !empty($request_content->password)) {
                // Get needed values
                $username = $request_content->username;
                $email = $request_content->email;
                $password = $request_content->password;
                $users = $user_repository->findAll();
                // Check values in database
                foreach ($users as $user) {
                    if (strcmp($user->getUsername(), $username) === 0) {
                        return new JsonResponse(array("message" => "User: Username already used"), 401);
                    }
                    if (strcmp($user->getEmail(), $email) === 0) {
                        return new JsonResponse(array("message" => "User: Email already used"), 401);
                    }
                }
                $password = hash("haval256,5", $password);
                $password = hash("md5", $password);
                $token = hash("haval256,5", time());
                // Put datas in database
                $user = new User();
                $user->setUsername($username);
                $user->setEmail($email);
                $user->setPassword($password);
                $user->setToken($token);
                $user_repository->add($user, true);
                return new JsonResponse(array("token" => $user->getToken()), 200);
            }
            return new JsonResponse(array("message" => "User: Missing field"), 400);
        }
    }
?>