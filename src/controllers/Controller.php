<?php
declare(strict_types=1);

namespace App\controllers;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;
use App\Model\Database;

abstract class Controller
{
    protected Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader('src/views');
        $this->twig = new Environment($loader,  ['debug' => true]);
        $this->twig->addExtension(new DebugExtension());
        $this->twig->addExtension(new IntlExtension());

    }
}
