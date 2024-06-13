<?php

// Chargement de l'autoloader pour charger automatiquement les classes

namespace App;

error_reporting(-1);

// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

require_once(__DIR__ . '/vendor/autoload.php');

// Initialisation de la session si nécessaire
session_start();

// Récupération du chemin de l'URL demandée
$path = $_SERVER['REQUEST_URI'];

// Instanciation du routeur et traitement de la requête
$router = new Router();
$router->callController($path);

