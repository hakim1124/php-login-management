<?php

namespace ProgramerZamanNow\Belajar\PHP\MVC\Service;

use PHPUnit\Framework\TestCase;
use ProgramerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgramerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgramerZamanNow\Belajar\PHP\MVC\Exception\ValidationException;
use ProgramerZamanNow\Belajar\PHP\MVC\Model\UserRegisterRequest;
use ProgramerZamanNow\Belajar\PHP\MVC\Model\UserLoginRequest;
use ProgramerZamanNow\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use ProgramerZamanNow\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use ProgramerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgramerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    protected function setUp(): void
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);
        $this->sessionRepository = new SessionRepository($connection);
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }
    /**@test */
    public function testRegisterSuccess()
    {
        $request = new UserRegisterRequest();
        $request->id = "hakim";
        $request->name = "hakim";
        $request->password = "hakim";
        $response = $this->userService->register($request);
        self::assertEquals($request->id, $response->user->id);
        self::assertEquals($request->name, $response->user->name);
        self::assertNotEquals($request->password, $response->user->password);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }
    /**@test */
    public function testRegisterFailed()
    {
        $this->expectException(ValidationException::class);
        $request = new UserRegisterRequest();
        $request->id = "";
        $request->name = "";
        $request->password = "";
        $this->userService->register($request);
    }
    /**@test */
    public function testRegisterDuplicate()
    {
        $user = new User();
        $user->id = "hakim";
        $user->name = "hakim";
        $user->password = "hakim";
        $this->userRepository->save($user);
        $this->expectException(ValidationException::class);
        $request = new UserRegisterRequest();
        $request->id = "hakim";
        $request->name = "hakim";
        $request->password = "hakim";
        $this->userService->register($request);
    }
    /**@test */
    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);
        $request = new UserLoginRequest();
        $request->id = "hakim";
        $request->password = "hakim";
        $this->userService->login($request);
    }
    /**@test */
    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->id = "hakim";
        $user->name = "Hakim";
        $user->password = password_hash("hakim", PASSWORD_BCRYPT);
        $this->expectException(ValidationException::class);
        $request = new UserLoginRequest();
        $request->id = "hakim";
        $request->password = "salah";
        $this->userService->login($request);
    }
    /**@test */
    public function testLoginSuccess()
    {
        $user = new User();
        $user->id = "hakim";
        $user->name = "Hakim";
        $user->password = password_hash("hakim", PASSWORD_BCRYPT);
        $this->expectException(ValidationException::class);
        $request = new UserLoginRequest();
        $request->id = "hakim";
        $request->password = "hakim";
        $response = $this->userService->login($request);
        self::assertEquals($request->id, $response->user->id);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }
    /**@test */
    public function testUpdateSuccess()
    {
        $user = new User();
        $user->id = "hakim";
        $user->name = "Hakim";
        $user->password = password_hash("hakim", PASSWORD_BCRYPT);
        $this->userRepository->save($user);
        $request = new UserProfileUpdateRequest();
        $request->id = "hakim";
        $request->name = "Zaidan";
        $this->userService->updateProfile($request);
        $result = $this->userRepository->findById($user->id);
        self::assertEquals($request->name, $result->name);
    }
    /**@test */
    public function testUpdateValidationError()
    {
        $this->expectException(ValidationException::class);
        $request = new UserProfileUpdateRequest();
        $request->id = "";
        $request->name = "";
        $this->userService->updateProfile($request);
    }
    /**@test */
    public function testUpdateNotFound()
    {
        $this->expectException(ValidationException::class);
        $request = new UserProfileUpdateRequest();
        $request->id = "hakim";
        $request->name = "Zaidan";
        $this->userService->updateProfile($request);
    }
    /**@test */
    public function testUpdatePasswordSuccess()
    {
        $user = new User();
        $user->id = "hakim";
        $user->name = "Hakim";
        $user->password = password_hash("hakim", PASSWORD_BCRYPT);
        $this->userRepository->save($user);
        $request = new UserPasswordUpdateRequest();
        $request->id = "hakim";
        $request->oldPassword = "hakim";
        $request->newPassword = "new";
        $this->userService->updatePassword($request);
        $result = $this->userRepository->findById($user->id);
        self::assertTrue(password_verify($request->newPassword, $result->password));
    }
    /**@test */
    public function testUpdatePasswordValidationError()
    {
        $this->expectException(ValidationException::class);
        $request = new UserPasswordUpdateRequest();
        $request->id = "hakim";
        $request->oldPassword = "";
        $request->newPassword = "";
        $this->userService->updatePassword($request);
    }
    /**@test */
    public function testUpdatePasswordWrongOldPassword()
    {
        $this->expectException(ValidationException::class);
        $user = new User();
        $user->id = "hakim";
        $user->name = "Hakim";
        $user->password = password_hash("hakim", PASSWORD_BCRYPT);
        $this->userRepository->save($user);
        $request = new UserPasswordUpdateRequest();
        $request->id = "hakim";
        $request->oldPassword = "salah";
        $request->newPassword = "new";
        $this->userService->updatePassword($request);
    }
    /**@test */
    public function testUpdatePasswordNotFound()
    {
        $this->expectException(ValidationException::class);
        $request = new UserPasswordUpdateRequest();
        $request->id = "hakim";
        $request->oldPassword = "hakim";
        $request->newPassword = "new";
        $this->userService->updatePassword($request);
    }
}
