<?php

namespace ProgramerZamanNow\Belajar\PHP\MVC\Repository;

use PHPUnit\Framework\TestCase;
use ProgramerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgramerZamanNow\Belajar\PHP\MVC\Domain\Session;
use ProgramerZamanNow\Belajar\PHP\MVC\Domain\User;

class SessionRepositoryTest extends TestCase
{
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;
    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository->deleteAll();
        $this->sessionRepository->deleteAll();
        $user = new User();
        $user->id = "hakim";
        $user->name = "Hakim";
        $user->password = "rahasia";
        $this->userRepository->save($user);
    }
    /**@test */
    public function testSaveSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "hakim";
        $this->sessionRepository->save($session);
        $result = $this->sessionRepository->findById($session->id);
        self::assertEquals($session->id, $result->id);
        self::assertEquals($session->userId, $result->userId);
    }
    /**@test */
    public function testDeleteByIdSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "hakim";
        $this->sessionRepository->save($session);
        $result = $this->sessionRepository->findById($session->id);
        self::assertEquals($session->id, $result->id);
        self::assertEquals($session->userId, $result->userId);
        $this->sessionRepository->deleteById($session->id);
        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }
    /**@test */
    public function testFindByIdNotFound()
    {
        $result = $this->sessionRepository->findById('not found');
        self::assertNull($result);
    }
}
