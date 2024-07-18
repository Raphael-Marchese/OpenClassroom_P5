<?php
declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\Controller;
use App\Model\Entity\User;
use App\Model\CSRFToken;
use App\Model\Repository\UserRepository;
use App\Service\FormSanitizer;
use App\Model\Validator\UserValidator;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController extends Controller
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

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function register(): void
    {
        $csrfToken = $this->token->generateToken('register');
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
            $sanitizedData = $this->formSanitizer->sanitize($_POST);
            $token = $sanitizedData['csrf_token'];
            $username = $sanitizedData['username'] ?? null ;
            $firstName = $sanitizedData['firstName'] ?? null ;
            $lastName = $sanitizedData['lastName'] ?? null ;
            $email = $sanitizedData['email'] ?? null ;
            $plainPassword = $sanitizedData['password'] ?? null ;
        }

        $password = $plainPassword ? password_hash($plainPassword, PASSWORD_DEFAULT) : $plainPassword;

        $user = new User(firstName: $firstName, lastName: $lastName, username: $username, email: $email, password: $password, role: "['ROLE_USER']");


        $validationErrors = array_merge($this->userValidator->validate($user), $this->token->validateToken($token, $csrfCheck));

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

}

