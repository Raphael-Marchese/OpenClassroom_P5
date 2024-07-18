<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\Controller;
use App\Exception\CSRFTokenException;
use App\Exception\UserException;
use App\Model\CSRFToken;
use App\Model\Repository\UserRepository;
use App\Model\Validator\ValidatorFactory;
use App\Service\FormSanitizer;
use App\Service\UserExtractor;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RegisterController extends Controller
{
    private UserRepository $repository;

    private FormSanitizer $formSanitizer;

    private CSRFToken $token;

    private UserExtractor $userExtractor;

    public function __construct()
    {
        parent::__construct();
        $this->repository = new UserRepository();
        $this->formSanitizer = new FormSanitizer();
        $this->token = new CSRFToken();
        $this->userExtractor = new UserExtractor();
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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }


        $sanitizedData = $this->formSanitizer->sanitize($_POST);

        $user = $this->userExtractor->extractUser($sanitizedData);

        try {
            $token = $sanitizedData['csrf_token'];

            $this->token->validateToken($token, $csrfCheck);

            ValidatorFactory::validate($user);
            $this->repository->save($user);

            echo $this->twig->render('user/success.html.twig');
        } catch (UserException|CSRFTokenException $e) {
            echo $this->twig->render('user/register.html.twig', [
                'errors' => $e->validationErrors,
                'formData' => [
                    'username' => $user->username,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'email' => $user->email,
                ]
            ]);
        } catch (\Exception $e) {
            echo $this->twig->render('user/register.html.twig', [
                'otherError' => $e->getMessage(),
                'formData' => [
                    'username' => $user->username,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'email' => $user->email
                ]
            ]);
        }
    }
}
