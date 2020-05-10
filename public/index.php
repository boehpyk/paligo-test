<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\controllers\IndexController;
use DI\Container;
use Slim\Views\Twig;
use App\services\UnzipService;
use App\services\FileUploadService;
use App\services\MessagesService;

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Create Container
$container = new Container();
AppFactory::setContainer($container);

$container->set('twig', function() {
    return Twig::create(__DIR__ . '/../src/views',
        [
            'cache' => false,
            'debug' => true
        ]);
});
$container->set('flash', function() {
    return new \Slim\Flash\Messages();
});
$container->set('unzip', function($container) {
    return new UnzipService($container);
});
$container->set('upload', function($container) {
    return new FileUploadService($container);
});
$container->set('messages', function($container) {
    return new MessagesService($container);
});


$app = AppFactory::create();
//$app = \DI\Bridge\Slim\Bridge::create();


$app->get('/', function (Request $request, Response $response, $args) use ($container) {
    $controller = new IndexController($request, $response, $container);
    return $controller->index();
});

$app->post('/upload', function ($request, $response, $args) use ($container) {
    $controller = new IndexController($request, $response, $container);
    return $controller->upload();
});

$app->run();