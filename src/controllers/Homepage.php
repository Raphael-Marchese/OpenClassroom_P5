<?php

namespace App\controllers;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Homepage extends Controller
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function render():void
    {
       echo $this->twig->render('homepage/homepage.html.twig');
    }

}
