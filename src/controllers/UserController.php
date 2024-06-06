<?php
declare(strict_types=1);

namespace App\controllers;

use App\entity\User;
use App\model\CSRFToken;
use App\model\repository\UserRepository;
use App\model\validator\FormValidator;
use App\model\validator\UserValidator;
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
        $csrfToken = CSRFToken::generateToken();
        echo $this->twig->render('user/register.html.twig', ['csrf_token' => $csrfToken]);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function submitRegister(): void
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sanitizedData = FormValidator::validate($_POST);
            $username = $sanitizedData['username'] ?? null ;
            $firstName = $sanitizedData['firstName'] ?? null ;
            $lastName = $sanitizedData['lastName'] ?? null ;
            $email = $sanitizedData['email'] ?? null ;
            $plainPassword = $sanitizedData['password'] ?? null ;
        }

        $password = $plainPassword ? password_hash($plainPassword, PASSWORD_DEFAULT) : $plainPassword;

        $user = new User(firstName: $firstName, lastName: $lastName, username: $username, email: $email, password: $password, role: "['ROLE_USER']");

        $validationErrors = UserValidator::validate($user);
            if (count($validationErrors) === 0) {
                $this->repository->save($user);
                try {
                    echo $this->twig->render('user/success.html.twig');
                } catch (LoaderError|RuntimeError|SyntaxError $e) {
                }
            } else {
                try {
                    echo $this->twig->render('user/register.html.twig', [
                        'emailError' => $validationErrors['email'],
                        'usernameError' => $validationErrors['username'],
                        'passwordError' => $validationErrors['password'],
                        'formData' => [
                            'username' => $username,
                            'firstName' => $firstName,
                            'lastName' => $lastName,
                            'email' => $email
                            ]
                        ]);
                } catch (LoaderError|RuntimeError|SyntaxError $e) {
                }
            }
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

