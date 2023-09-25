<?php

namespace ProgramerZamanNow\Belajar\PHP\MVC\Middleware;

use ProgramerZamanNow\Belajar\PHP\MVC\App\View;
use ProgramerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgramerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgramerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgramerZamanNow\Belajar\PHP\MVC\Service\SessionService;

class MustLoginMiddleware implements Middleware
{
    private SessionService $sessionService;
    public function __construct()
    {
        $sessionRepository = new SessionRepository(Database::getConnection());
        $userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }
    function before(): void
    {
        $user = $this->sessionService->current();
        if ($user == null) {
            View::redirect('http://localhost/php-login-management/public/users/login');
        }
    }
}
