<?php
class ControllerUser extends Controller {
    public function __construct(AltoRouter $router) {
        parent::__construct($router);
    }
    // Handle user login
    public function login() {
        $model = new ModelUser();
        $model->isConnected();
        // On peut maintenant accéder à $this->router
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (! empty($_POST['mail']) && ! empty($_POST['password'])) {
                $user = $model->getUser($_POST['mail']);
                if ($user && password_verify($_POST['password'], $user->getPassword())) {
                    $_SESSION['id']   = $user->getId_user();
                    $_SESSION['name'] = $user->getUsername();
                    header('Location: ' . $this->router->generate('home'));
                    exit();
                } else {
                    $error = 'Email or password is not valid';
                    require_once './view/login.php';
                }
            } else {
                $error = 'Fields are incorrect';
                require_once './view/login.php';
            }
        } else {
            require_once './view/login.php';
        }
    }

    // Handle user logout
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ' . $this->router->generate('home'));
    }

    // Handle user registration
    public function register() {
        $token = bin2hex(random_bytes(32));
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (! empty($_POST['email']) && ! empty($_POST['password']) && ! empty($_POST['password_verify'])) {
                if ($_POST['password'] === $_POST['password_verify']) {
                    $model = new ModelUser();
                    if ($model->checkUserMail($_POST['email']) && $model->checkUserName($_POST['username'])) {
                        $username = $_POST['username'];
                        $email = $_POST['email'];
                        $password = $_POST['password'];

                        $insertResult = $model->createUser($username, $email, $password, $token);

                            // Envoyer l'email de vérification
                            $mailer = new Mailer($token);
                            $verificationLink = "http://localhost/nihon/verify/?email=$email&code=$token";
                            $sendResult = $mailer->sendVerificationEmail($email, $username, $verificationLink);
                            var_dump($token);
    
                        echo "Compte crée avec succès !";

                        require_once './view/home.php';
                    } else {
                        echo "Email or username is already taken.";
                        require_once './view/login.php';
                    }
                } else {
                    echo 'Passwords do not match.';
                    require_once './view/register.php';
                }
            } else {
                echo 'Email, password, and password verify are required.';
                require_once './view/register.php';
            }
        } else {
            require_once './view/register.php';
        }
    }

    public function verify(){
        $token = $_GET['code'] ?? '';
        $email = $_GET['email'] ?? '';
        if($token && $email){

            $model = new ModelUser();
            if($model->verifyToken($token, $email)){
                echo 'Everything went fine';
                header('Location: /nihon/login');
            } else {
                echo 'There was a problem when you tried to log in';
            }
            
        } else {
            echo 'Check email again';
        }
    }

}