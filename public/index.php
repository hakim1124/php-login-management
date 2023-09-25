<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ProgramerZamanNow\Belajar\PHP\MVC\App\Router;
use ProgramerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgramerZamanNow\Belajar\PHP\MVC\Controller\HomeController;
use ProgramerZamanNow\Belajar\PHP\MVC\Controller\UserController;
use ProgramerZamanNow\Belajar\PHP\MVC\Middleware\MustLoginMiddleware;
use ProgramerZamanNow\Belajar\PHP\MVC\Middleware\MustNotLoginMiddleware;

Database::getConnection('prod');
/**
 * routing HomeControler
 */
Router::add('GET', '/', HomeController::class, 'index', []); //menghubungkan router dan controller

/**
 * routing UserController
 */
Router::add('GET', '/users/register', UserController::class, 'register', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/register', UserController::class, 'postRegister', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/login', UserController::class, 'login', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/login', UserController::class, 'postLogin', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/logout', UserController::class, 'logout', [MustLoginMiddleware::class]);
Router::add('GET', '/users/profile', UserController::class, 'updateProfile', [MustLoginMiddleware::class]);
Router::add('POST', '/users/profile', UserController::class, 'postUpdateProfile', [MustLoginMiddleware::class]);
Router::add('GET', '/users/password', UserController::class, 'updatePassword', [MustLoginMiddleware::class]);
Router::add('POST', '/users/password', UserController::class, 'postUpdatePassword', [MustLoginMiddleware::class]);
Router::run();
