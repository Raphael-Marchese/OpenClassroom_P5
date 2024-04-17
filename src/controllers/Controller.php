<?php
declare(strict_types=1);

namespace App\controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class Controller
{
    protected Environment $twig;
    public function __construct()
    {
        $loader = new FilesystemLoader('src/views');
        $this->twig = new Environment($loader);
    }

}