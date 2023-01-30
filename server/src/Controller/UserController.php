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
            if (!empty($request->query->get("password")) && !empty($request->query->get("login"))) {
                $login = $request->query->get("login");
                $password = $request->query->get("password");
                $password = hash("haval256,5", $password);
                $password = hash("md5", $password);
                $users = $user_repository->findAll();
                foreach ($users as $user) {
                    if (strcmp($user->getPassword(), $password) === 0 &&
                        (strcmp($user->getUsername(), $login) === 0 || strcmp($user->getEmail(), $login) === 0)) {
                        return new JsonResponse(array("user_id" => $user->getId()), 200);
                    }
                }
                return new JsonResponse(array("message" => "Wrong password"), 401);
            }
            return new JsonResponse(array("message" => "Missing field"), 400);
        }

        /**
         * @Route("/register", name="user_register")
         */
        public function register(Request $request, UserRepository $user_repository)
        {
            if (!empty($request->query->get("username")) && !empty($request->query->get("email")) && !empty($request->query->get("password"))) {
                $username = $request->query->get("username");
                $email = $request->query->get("email");
                $password = $request->query->get("password");
                $users = $user_repository->findAll();
                foreach ($users as $user) {
                    if (strcmp($user->getUsername(), $username) === 0) {
                        return new JsonResponse(array("message" => "Username already used"), 401);
                    }
                    if (strcmp($user->getEmail(), $email) === 0) {
                        return new JsonResponse(array("message" => "Email already used"), 401);
                    }
                }
                $password = hash("haval256,5", $password);
                $password = hash("md5", $password);
                $user = new User();
                $user->setUsername($username);
                $user->setEmail($email);
                $user->setPassword($password);
                $user_repository->add($user, true);
                return new JsonResponse(array("user_id" => $user->getId()), 200);
            }
            return new JsonResponse(array("message" => "Missing field"), 400);
        }
    }
?>