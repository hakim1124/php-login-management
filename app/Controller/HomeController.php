<?php

namespace ProgramerZamanNow\Belajar\PHP\MVC\Controller;

use ProgramerZamanNow\Belajar\PHP\MVC\App\View;
use ProgramerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgramerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgramerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgramerZamanNow\Belajar\PHP\MVC\Service\SessionService;

class HomeController
{
    private SessionService $sessionService;
    public function __construct()
    {
        $connection = Database::getConnection();
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }
    function index()
    {
        $user = $this->sessionService->current();
        if ($user == null) {
            View::render('Home/index', ["title" => "PHP Login Management"]);
        } else {
            View::render('Home/dashboard', ["title" => "Dashboard", "user" => ["name" => $user->name]]);
        }
    }
}
