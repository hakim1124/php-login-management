<?php

namespace ProgramerZamanNow\Belajar\PHP\MVC\Controller {
    require_once __DIR__ . '/../Helper/helper.php';

    use PHPUnit\Framework\TestCase;
    use ProgramerZamanNow\Belajar\PHP\MVC\Config\Database;
    use ProgramerZamanNow\Belajar\PHP\MVC\Domain\Session;
    use ProgramerZamanNow\Belajar\PHP\MVC\Domain\User;
    use ProgramerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
    use ProgramerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
    use ProgramerZamanNow\Belajar\PHP\MVC\Service\SessionService;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;
        protected function setUp(): void
        {
            $this->userController = new UserController();
            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();
            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();
            putenv("mode=test");
        }
        /**@test */
        public function testRegister()
        {
            $this->userController->register();
            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
        }
        /**@test */
        public function testPostRegisterSuccess()
        {
            $_POST['id'] = "hakim";
            $_POST['name'] = "Hakim";
            $_POST['password'] = "rahasia";
            $this->userController->postRegister();
            $this->expectOutputRegex("[Location: /users/login]");
        }
        /**@test */
        public function testPostRegisterValidationError()
        {
            $_POST['id'] = "";
            $_POST['name'] = "Hakim";
            $_POST['password'] = "rahasia";
            $this->userController->postRegister();
            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[Id, Name, Password can not blank]");
        }
        /**@test */
        public function testPostRegisterDuplicate()
        {
            $user = new User();
            $user->id = "hakim";
            $user->name = "Hakim";
            $user->password = "rahasia";
            $this->userRepository->save($user);
            $_POST['id'] = "hakim";
            $_POST['name'] = "Hakim";
            $_POST['password'] = "rahasia";
            $this->userController->postRegister();
            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[user sudah ada]");
        }

        /**@test */
        public function testLogin()
        {
            $this->userController->login();
            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
        }
        /**@test */
        public function testLoginSuccess()
        {
            $user = new User();
            $user->id = "hakim";
            $user->name = "Hakim";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);
            $_POST['id'] = "hakim";
            $_POST['password'] = "rahasia";
            $this->userController->postLogin();
            $this->expectOutputRegex("[Location: /public/index]");
            $this->expectOutputRegex("[X-PZN-SESSION: ]");
        }
        /**@test */
        public function testLoginValidationError()
        {
            $_POST['id'] = '';
            $_POST['password'] = '';
            $this->userController->postLogin();
            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id, Password can not blank]");
        }
        /**@test */
        public function testLoginUserNotFound()
        {
            $_POST['id'] = 'notfound';
            $_POST['password'] = 'notfound';
            $this->userController->postLogin();
            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[id atau password salah]");
        }
        /**@test */
        public function testLoginWrongPassword()
        {
            $user = new User();
            $user->id = "hakim";
            $user->name = "Hakim";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);
            $_POST['id'] = 'hakim';
            $_POST['password'] = 'salah';
            $this->userController->postLogin();
            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[id atau password salah]");
        }
        /**@test */
        public function testLogout()
        {
            $user = new User();
            $user->id = "hakim";
            $user->name = "Hakim";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);
            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);
            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
            $this->userController->logout();
            $this->expectOutputRegex("[Location: /public/index]");
            $this->expectOutputRegex("[X-PZN-SESSION: ]");
        }
        /**@test */
        public function testUpdateProfile()
        {
            $user = new User();
            $user->id = "hakim";
            $user->name = "Hakim";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);
            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);
            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
            $this->userController->updateProfile();
            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[hakim]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Hakim]");
        }
        /**@test */
        public function testPostUpdateProfileSuccess()
        {
            $user = new User();
            $user->id = "hakim";
            $user->name = "Hakim";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);
            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);
            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
            $_POST['name'] = 'Budi';
            $this->userController->postUpdateProfile();
            $this->expectOutputRegex("[Location: /public/index]");
            $result = $this->userRepository->findById("hakim");
            self::assertEquals("Budi", $result->name);
        }
        /**@test */
        public function testPostUpdateProfileValidationError()
        {
            $user = new User();
            $user->id = "hakim";
            $user->name = "Hakim";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);
            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);
            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
            $_POST['name'] = '';
            $this->userController->postUpdateProfile();
            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[hakim]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Id, Name can not blank]");
        }
        /**@test */
        public function testUpdatePassword()
        {
            $user = new User();
            $user->id = "hakim";
            $user->name = "Hakim";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);
            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);
            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
            $this->userController->updatePassword();
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[hakim]");
        }
        /**@test */
        public function testPostUpdatePasswordSuccess()
        {
            $user = new User();
            $user->id = "hakim";
            $user->name = "Hakim";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);
            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);
            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
            $_POST['oldPassword'] = 'rahasia';
            $_POST['newPassword'] = 'budi';
            $this->userController->postUpdatePassword();
            $this->expectOutputRegex("[Location: /public/index]");
            $result = $this->userRepository->findById($user->id);
            self::assertTrue(password_verify("budi", $result->password));
        }
        /**@test */
        public function testPostUpdatePasswordValidationError()
        {
            $user = new User();
            $user->id = "hakim";
            $user->name = "Hakim";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);
            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);
            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
            $_POST['oldPassword'] = '';
            $_POST['newPassword'] = '';
            $this->userController->postUpdatePassword();
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[hakim]");
            $this->expectOutputRegex("[Id, Old Password, New Password can not blank]");
        }
        /**@test */
        public function testPostUpdatePasswordWrongOldPassword()
        {
            $user = new User();
            $user->id = "hakim";
            $user->name = "Hakim";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);
            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);
            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
            $_POST['oldPassword'] = 'salah';
            $_POST['newPassword'] = 'budi';
            $this->userController->postUpdatePassword();
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[hakim]");
            $this->expectOutputRegex("[Old password is wrong]");
        }
    }
}
