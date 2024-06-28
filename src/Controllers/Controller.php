<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Model\Service\UserProvider;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

abstract class Controller
{
    protected Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader('src/Views');
        $this->twig = new Environment($loader,  ['debug' => true]);
        $this->twig->addExtension(new DebugExtension());
        $this->twig->addExtension(new IntlExtension());
        if (isset($_SESSION['LOGGED_USER'])) {
            $this->twig->addGlobal('session', $_SESSION['LOGGED_USER']);
        }
        $_SESSION['TOKEN'] = bin2hex(random_bytes(35));
    }
}
