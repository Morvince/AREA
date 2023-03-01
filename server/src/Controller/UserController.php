<?php
    namespace App\Controller;
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use App\Entity\User;
    use App\Repository\UserRepository;
    use App\Repository\ServerRepository;
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
            header('Access-Control-Allow-Origin: *');
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
            header('Access-Control-Allow-Origin: *');
            $request_content = json_decode($request->getContent());
            if (!empty($request_content->username) && !empty($request_content->email) && !empty($request_content->password)) {
                // Get needed values
                $username = $request_content->username;
                $email = $request_content->email;
                if ($this->checkEmail($email) === false) {
                    return new JsonResponse(array("message" => "User: Invalid email"), 400);
                }
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
                // Put datas in database
                $user = new User();
                $user->setUsername($username);
                $user->setEmail($email);
                $user->setPassword($password);
                $user->setToken($this->generateToken());
                $user->setValidate(false);
                $user_repository->add($user, true);
                return new JsonResponse(array("token" => $user->getToken()), 200);
            }
            return new JsonResponse(array("message" => "User: Missing field"), 400);
        }
        private function generateToken()
        {
            return hash("haval256,5", time());
        }
        private function checkEmail($email)
        {
            // Check email pattern
            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                return false;
            }
            // Check email domain
            list($user, $domain) = explode("@", $email);
            if (!checkdnsrr($domain, "MX")) {
                return false;
            }
            return true;
        }

        /**
         * @Route("/send_confirmation", name="user_send_confirmation")
         */
        public function sendConfirmationMail(Request $request, UserRepository $user_repository, ServerRepository $server_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->token)) {
                return new JsonResponse(array("message" => "User: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "User: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            if (empty($server_repository->findAll())) {
                return new JsonResponse(array("message" => "User: No server informations"), 400);
            }
            $server = $server_repository->findAll()[0];
            $mail = new PHPMailer(true);
            try {
                // Server SMTP configuration
                $mail->isSMTP();
                $mail->Host = $server->getHost();
                $mail->SMTPAuth = true;
                $mail->Username = $server->getEmail();
                $mail->Password = base64_decode($server->getPassword());
                $mail->SMTPSecure = $server->getSMTPSecure();
                $mail->Port = $server->getPort();
                // Set mail informations
                $mail->setFrom($server->getEmail());
                $mail->addAddress($user->getEmail(), $user->getUsername());
                $mail->Subject = "Hapilink - Register confirmation";
                $mail->Body = "";
                $mail->Timeout = 30;
                // Send mail
                $mail->send();
            } catch (Exception $e) {
                return new JsonResponse(array("message" => "KO"), 400);
            }
            return new JsonResponse(array("message" => "OK"), 200);
        }

        /**
         * @Route("/validate", name="user_validate")
         */
        public function validate(Request $request, UserRepository $user_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->token)) {
                return new JsonResponse(array("message" => "User: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Spotify: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            $user->setValidate(true);
            $user->setToken($this->generateToken());
            $user_repository->add($user, true);
            return new JsonResponse(array("token" => $user->getToken()), 200);
        }
        /**
         * @Route("/validated", name="user_validated")
         */
        public function isUserValidated(Request $request, UserRepository $user_repository)
        {
            header('Access-Control-Allow-Origin: *');
            // Get needed values
            $request_content = json_decode($request->getContent());
            if (empty($request_content->token)) {
                return new JsonResponse(array("message" => "User: Missing field"), 400);
            }
            $token = $request_content->token;
            if (empty($user_repository->findByToken($token))) {
                return new JsonResponse(array("message" => "Spotify: Bad auth token"), 400);
            }
            $user = $user_repository->findByToken($token)[0];
            return new JsonResponse(array("validated" => $user->getValidate()), 200);
        }
    }
?>