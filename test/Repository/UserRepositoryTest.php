<?php

namespace ProgramerZamanNow\Belajar\PHP\MVC\Repository;

use PHPUnit\Framework\TestCase;
use ProgramerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgramerZamanNow\Belajar\PHP\MVC\Domain\User;


class UserRepositoryTest extends TestCase
{
    /**@test */
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }
    /**@test */
    public function testSaveSuccess()
    {
        $user = new User();
        $user->id = "hakim";
        $user->name = "hakim";
        $user->password = "hakim";

        $this->userRepository->save($user);
        $result = $this->userRepository->findById($user->id);
        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }
    /**@test */
    public function testFindByIdNotFound()
    {
        $user = $this->userRepository->findById("not found");
        self::assertNull($user);
    }
    /**@test */
    public function testUpdate()
    {
        $user = new User();
        $user->id = "hakim";
        $user->name = "hakim";
        $user->password = "hakim";
        $this->userRepository->save($user);
        $user->name = "Budi";
        $this->userRepository->update($user);
        $result = $this->userRepository->findById($user->id);
        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }
}
