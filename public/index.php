<?php
declare (strict_types = 1);
if (session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

use MyApp\Service\DependencyContainer;
use MyApp\Extension\SessionExtension;
use MyApp\Routing\Router;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../', '.env.local');
$dotenv->load();

$container = new DependencyContainer();
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

$twig->addGlobal('session', $_SESSION);
$twig->addExtension(new SessionExtension());

$router = new Router($container);
$router->route($twig);