<?php

declare(strict_types=1);

namespace App\Controller\Contact;

use App\Controller\Controller;
use App\Exception\ContactException;
use App\Model\Contact\Contact;
use App\Model\Validator\ContactValidator;
use App\Service\FormSanitizer;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use App\Config;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


class ContactController extends Controller
{

    private MailerInterface $mailer;

    private FormSanitizer $formSanitizer;

    private ContactValidator $contactValidator;

    public function __construct()
    {
        parent::__construct();
        $transport = Transport::fromDsn(Config::DSN);
        $this->mailer = new Mailer($transport);
        $this->formSanitizer = new FormSanitizer();
        $this->contactValidator = new ContactValidator();
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function contact(): void
    {
        echo $this->twig->render('contact/contact.html.twig');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     * @throws SyntaxError
     * @throws \Exception
     */
    public function submitContact(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $contact = null;

        try {
            $senderEmail = $_POST["email"];
            $firstName = $_POST["firstName"];
            $lastName = $_POST["lastName"];
            $message = $_POST["message"];
            $subject = $_POST["subject"] ?: 'Demande de contact';

            $contact = new Contact(
                email: $senderEmail,
                message: $message,
                firstName: $firstName,
                lastName: $lastName,
                subject: $subject
            );

            $this->contactValidator->validate($contact);

            $email = (new Email())
                ->from($contact->email)
                ->to('marchese.raphael@gmail.com')
                ->subject($contact->subject)
                ->text("{$contact->firstName} {$contact->lastName}  {$contact->message}")
                ->html(
                    "<h1 style='font-weight: bold; font-size: 1.2rem'>{$contact->firstName} {$contact->lastName} </h1>  <br/> <p>{$contact->message} </p>"
                );

            $this->mailer->send($email);

            echo $this->twig->render('contact/success.html.twig');
        } catch (ContactException $e) {
            $validationErrors = $e->validationErrors;
            echo $this->twig->render('contact/contact.html.twig', [
                'errors' => $validationErrors,
                'formData' => [
                    'email' => $contact->email,
                    'firstName' => $contact->firstName,
                    'lastName' => $contact->lastName,
                    'message' => $contact->message,
                    'subject' => $contact->subject,
                ]
            ]);
        } catch (TransportException $e) {
            $errors = $e->getMessage();

            echo $this->twig->render('contact/contact.html.twig', [
                'otherError' => $errors,
                'formData' => [
                    'email' => $contact->email,
                    'firstName' => $contact->firstName,
                    'lastName' => $contact->lastName,
                    'message' => $contact->message,
                    'subject' => $contact->subject,
                ]
            ]);
        }
    }
}
