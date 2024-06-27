<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entity\User;
use App\Model\CSRFToken;
use App\Model\Repository\UserRepository;
use App\Model\Validator\FormSanitizer;
use App\Model\Validator\UserValidator;
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
        $csrfToken = CSRFToken::generateToken('register');
        echo $this->twig->render('user/register.html.twig', ['csrf_token' => $csrfToken]);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function submitRegister(): void
    {
        $csrfCheck = 'register';
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token'])) {
            $sanitizedData = FormSanitizer::sanitize($_POST);
            $token = $sanitizedData['csrf_token'];
            $username = $sanitizedData['username'] ?? null ;
            $firstName = $sanitizedData['firstName'] ?? null ;
            $lastName = $sanitizedData['lastName'] ?? null ;
            $email = $sanitizedData['email'] ?? null ;
            $plainPassword = $sanitizedData['password'] ?? null ;
        }

        $password = $plainPassword ? password_hash($plainPassword, PASSWORD_DEFAULT) : $plainPassword;

        $user = new User(firstName: $firstName, lastName: $lastName, username: $username, email: $email, password: $password, role: "['ROLE_USER']");


        $validationErrors = array_merge(UserValidator::validate($user), CSRFToken::validateToken($token, $csrfCheck));

            if (count($validationErrors) === 0) {
                $this->repository->save($user);
                try {
                    echo $this->twig->render('user/success.html.twig');
                } catch (LoaderError|RuntimeError|SyntaxError $e) {
                    echo $this->twig->render('user/register.html.twig', [
                        'twigError' => $e,
                        'formData' => [
                            'username' => $username,
                            'firstName' => $firstName,
                            'lastName' => $lastName,
                            'email' => $email
                        ]
                    ]);
                }
            } else {
                try {
                    echo $this->twig->render('user/register.html.twig', [
                        'errors' => $validationErrors,
                        'formData' => [
                            'username' => $username,
                            'firstName' => $firstName,
                            'lastName' => $lastName,
                            'email' => $email
                            ]
                        ]);
                } catch (LoaderError|RuntimeError|SyntaxError $e) {
                    echo $this->twig->render('user/register.html.twig', [
                        'twigError' => $e,
                        'formData' => [
                            'username' => $username,
                            'firstName' => $firstName,
                            'lastName' => $lastName,
                            'email' => $email
                        ]
                    ]);
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

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return;
        }
        $postData = $_POST;
        if (!isset($postData['email']) ||  !isset($postData['password'])) {
            $errorMessage = 'Vous devez renseigner un email et un mot de passe.';
            echo $this->twig->render('user/login.html.twig', ['errorMessage' => $errorMessage]);
        }

        $sanitizedData = FormSanitizer::sanitize($postData);

        if (!filter_var($sanitizedData['email'], FILTER_VALIDATE_EMAIL)) {
            $errorMessage = 'Il faut un email valide pour soumettre le formulaire.';
            echo $this->twig->render('user/login.html.twig', ['errorMessage' => $errorMessage]);
        }

        $user = $this->repository->findByEmail($sanitizedData['email']);

        if ($user['email'] === $sanitizedData['email'] &&
            password_verify($sanitizedData['password'],$user['password'])
        ) {
            $_SESSION['LOGGED_USER'] = [
                'email' => $user['email'],
                'user_id' => $user['id'],
                'username' => $user['username'],
            ];
            $this->twig->addGlobal('session', $_SESSION['LOGGED_USER']);
            echo $this->twig->render('homepage/homepage.html.twig');
        }
        if (!isset($_SESSION['LOGGED_USER'])) {
            $errorMessage = 'Les identifiants de connexions ne sont pas valides.';
            echo $this->twig->render('user/login.html.twig', ['errorMessage' => $errorMessage]);
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

