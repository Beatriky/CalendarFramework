<?php

/** @var Router $router */
global $router;

/** @var Container $container */
global $container;

use App\Controllers\ReservationController;
use App\Controllers\{Auth\LoginController, Auth\LogoutController, Auth\RegisterController, HomeController};
use App\Middleware\{Authenticated, Guest};
use League\{Container\Container, Route\RouteGroup, Route\Router};


// Routes that need authentication in order to access
$router->group('', function (RouteGroup $router) {

    $router->get('/', [HomeController::class, 'index'])->setName('home');

    $router->post('/logout', [LogoutController::class, 'logout'])->setName('logout');

  //  $router->post('/reservation-test', [ReservationController::class, 'store'])->setName('reservation.twig');

    $router->post('/reservation', [ReservationController::class, 'store'])->setName('reservation.store');


   // $router->get('/appointment', [ReservationController::class, 'index'])->setName('appointment');
   // $router->post('/appointment', [ReservationController::class, 'store'])->setName('appointment.store');

})->middleware($container->get(Authenticated::class));

// Routes that can be accessed only if the user is NOT authenticated
$router->group('', function (RouteGroup $router) {

    $router->get('/login', [LoginController::class, 'index'])->setName('login');

    $router->post('/login', [LoginController::class, 'store'])->setName('login.store');

    $router->get('/register', [RegisterController::class, 'index'])->setName('register');

    $router->post('/register', [RegisterController::class, 'store'])->setName('register.store');

})->middleware($container->get(Guest::class));
