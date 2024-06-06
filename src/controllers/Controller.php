<?php
declare(strict_types=1);

namespace App\controllers;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

abstract class Controller
{
    protected Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader('src/views');
        $this->twig = new Environment($loader,  ['debug' => true]);
        $this->twig->addExtension(new DebugExtension());
        $this->twig->addExtension(new IntlExtension());
        if (isset($_SESSION['LOGGED_USER'])) {
            $this->twig->addGlobal('session', $_SESSION['LOGGED_USER']);
        }
        if (isset($_SESSION['TOKEN'])) {
            $this->twig->addGlobal('token', $_SESSION['TOKEN']);
        } else {
            $_SESSION['TOKEN'] = bin2hex(random_bytes(35));
            $this->twig->addGlobal('token', $_SESSION['TOKEN']);
        }
    }
}
