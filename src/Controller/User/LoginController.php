<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\Controller;
use App\Model\CSRFToken;
use App\Model\Repository\UserRepository;
use App\Service\FormSanitizer;
use App\Model\Validator\UserValidator;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class LoginController extends Controller
{
    private UserRepository $repository;

    private FormSanitizer $formSanitizer;

    private UserValidator $userValidator;

    private CSRFToken $token;

    public function __construct()
    {
        parent::__construct();
        $this->repository = new UserRepository();
        $this->formSanitizer = new FormSanitizer();
        $this->userValidator = new UserValidator();
        $this->token = new CSRFToken();
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        $postData = $_POST;
        if (!isset($postData['email']) || !isset($postData['password'])) {
            $errorMessage = 'Vous devez renseigner un email et un mot de passe.';
            echo $this->twig->render('user/login.html.twig', ['errorMessage' => $errorMessage]);
        }

        $sanitizedData = $this->formSanitizer->sanitize($postData);

        if (!filter_var($sanitizedData['email'], FILTER_VALIDATE_EMAIL)) {
            $errorMessage = 'Il faut un email valide pour soumettre le formulaire.';
            echo $this->twig->render('user/login.html.twig', ['errorMessage' => $errorMessage]);
        }

        $user = $this->repository->findByEmail($sanitizedData['email']);

        if ($user['email'] === $sanitizedData['email'] &&
            password_verify($sanitizedData['password'], $user['password'])
        ) {
            $_SESSION['LOGGED_USER'] = [
                'email' => $user['email'],
                'user_id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
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
