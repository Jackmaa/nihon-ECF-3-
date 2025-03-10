<?php
class ControllerAdmin extends Controller {
    public function __construct(AltoRouter $router) {
        parent::__construct($router);
    }

    // Admin login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (! isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Invalid CSRF Token");
            }

            $credential = $_POST['credential'];
            $password   = $_POST['password'];

            $model = new ModelUser();
            $user  = $model->getUser($credential);
            if (password_verify($password, $user->getPassword()) && $user->getId_role() === 2) {
                session_unset();
                session_start();
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['id_admin']        = $user->getId_user();

                header('Location: ' . $this->router->generate('admin_dashboard'));
                exit;
            } else {
                echo 'Invalid email or password.';
            }
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        require_once './view/admin_login.php';
    }

    // Admin dashboard
    public function dashboard() {
        if (! isset($_SESSION['admin_logged_in'])) {
            header('Location: ' . $this->router->generate('admin_login'));
            exit;
        }

        require_once './view/admin_dashboard.php';
    }

    // Search for a manga
    public function searchManga() {
        $search        = '%' . $_POST['search'] . '%';
        $model         = new ModelManga();
        $searchResults = $model->searchAdminManga($search);
        if (empty($searchResults)) {
            $searchResults = ['error' => 'No results found'];
        }
        header('Content-Type: application/json');
        echo json_encode($searchResults, JSON_PRETTY_PRINT);
    }

    // Search for a user
    public function searchUser() {
        $search        = '%' . $_POST['search'] . '%';
        $model         = new ModelUser();
        $searchResults = $model->searchUser($search);
        if (empty($searchResults)) {
            $searchResults = ['error' => 'No results found'];
        }
        header('Content-Type: application/json');
        echo json_encode($searchResults, JSON_PRETTY_PRINT);
    }

    //Create a User if no results found
    public function createUser() {
        $token = bin2hex(random_bytes(32));
        $email = $_POST['email'];
        $model = new ModelUser();
        $model->createUserByAdmin($email, $token);

        $mailer = new Mailer($token);
        $link   = "http://nihon/finishsignup/?email=$email&code=$token";
        $mailer->sendFinishSignupEmail($email, $link);
    }
}