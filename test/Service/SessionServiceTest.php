<?php

namespace ProgramerZamanNow\Belajar\PHP\MVC\Service;

require_once __DIR__ . '/../Helper/helper.php';

use PHPUnit\Framework\TestCase;
use ProgramerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgramerZamanNow\Belajar\PHP\MVC\Domain\Session;
use ProgramerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgramerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgramerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;


class SessionServiceTest extends TestCase
{
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;
    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
        $user = new User();
        $user->id = "hakim";
        $user->name = "Hakim";
        $user->password = "rahasia";
        $this->userRepository->save($user);
    }
    /**@test */
    public function testCreate()
    {
        $session = $this->sessionService->create("hakim");
        $this->expectOutputRegex("[X-PZN-SESSION: $session->id]");
        $result = $this->sessionRepository->findById($session->id);
        self::assertEquals("hakim", $result->userId);
    }
    /**@test */
    public function testDestroy()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "hakim";
        $this->sessionRepository->save($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        $this->sessionService->destroy();
        $this->expectOutputRegex("[X-PZN-SESSION: ]");
        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }
    /**@test */
    public function testCurrent()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "hakim";
        $this->sessionRepository->save($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        $user = $this->sessionService->current();
        self::assertEquals($session->userId, $user->id);
    }
}
