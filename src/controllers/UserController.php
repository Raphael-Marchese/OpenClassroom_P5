<?php
declare(strict_types=1);

namespace App\controllers;

use App\model\UserRepository;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController extends Controller
{
    private UserRepository $repository;
    public function __construct()
    {
        parent::__construct();
        $this->repository = new UserRepository();
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function register(): void
    {
        echo $this->twig->render('user/register.html.twig');
    }

    /**
     * @return void
     */
    public function submitRegister(): void
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? null ;
            $firstName = $_POST['firstName'] ?? null ;
            $lastName = $_POST['lastName'] ?? null ;
            $email = $_POST['email'] ?? null ;
            $plainPassword = $_POST['password'] ?? null ;
        }

        $this->repository->save(username: $username, firstName: $firstName, lastName: $lastName, email: $email, plainPassword: $plainPassword);

        echo $this->twig->render('user/success.html.twig');;
    }

    public function login(): void
    {
        echo $this->twig->render('user/login.html.twig');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function submitLogin(): void
    {
        $users = $this->repository->findAll();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $_POST;
            if (isset($postData['email']) &&  isset($postData['password'])) {
                if (!filter_var($postData['email'], FILTER_VALIDATE_EMAIL)) {
                    $errorMessage = 'Il faut un email valide pour soumettre le formulaire.';
                } else {
                    foreach ($users as $user) {
                        if (
                            $user['email'] === $postData['email'] &&
                            password_verify($postData['password'],$user['password'])
                        ) {
                            $_SESSION['LOGGED_USER'] = [
                                'email' => $user['email'],
                                'user_id' => $user['id'],
                                'username' => $user['username'],
                            ];
                            $this->twig->addGlobal('session', $_SESSION['LOGGED_USER']);
                            echo $this->twig->render('homepage/homepage.html.twig');
                        }
                    }

                    if (!isset($_SESSION['LOGGED_USER'])) {
                        $errorMessage = 'Les identifiants de connexions ne sont pas valides.';
                       echo $this->twig->render('user/login.html.twig', ['errorMessage' => $errorMessage]);
                    }
                }
            }
        }
    }

    public function logout(): void
    {
        $this->twig->addGlobal('session', null);
        unset($_SESSION['LOGGED_USER']);
        session_destroy();
        echo $this->twig->render('homepage/homepage.html.twig');
    }
}

