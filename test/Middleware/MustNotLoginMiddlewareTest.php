<?php


namespace ProgramerZamanNow\Belajar\PHP\MVC\Middleware {
    require_once __DIR__ . '/../Helper/helper.php';

    use PHPUnit\Framework\TestCase;
    use ProgramerZamanNow\Belajar\PHP\MVC\Config\Database;
    use ProgramerZamanNow\Belajar\PHP\MVC\Domain\Session;
    use ProgramerZamanNow\Belajar\PHP\MVC\Domain\User;
    use ProgramerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
    use ProgramerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
    use ProgramerZamanNow\Belajar\PHP\MVC\Service\SessionService;

    class MustNotLoginMiddlewareTest extends TestCase
    {
        private MustNotLoginMiddleware $middleware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;
        /**@test */
        protected function setUp(): void
        {
            $this->middleware = new MustNotLoginMiddleware();
            putenv("mode=test");
            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }
        /**@test */
        public function testBeforeGuest()
        {
            $this->middleware->before();
            $this->expectOutputString("");
        }
        /**@test */
        public function testBeforeLoginUser()
        {
            $user = new User();
            $user->id = "eko";
            $user->name = "Eko";
            $user->password = "rahasia";
            $this->userRepository->save($user);
            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);
            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
            $this->middleware->before();
            $this->expectOutputRegex('[Location: http://localhost/php-login-management/public]');
        }
    }
}
