<?php

namespace ProgramerZamanNow\Belajar\PHP\MVC\Controller;

use ProgramerZamanNow\Belajar\PHP\MVC\App\View;
use ProgramerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgramerZamanNow\Belajar\PHP\MVC\Exception\ValidationException;
use ProgramerZamanNow\Belajar\PHP\MVC\Model\UserLoginRequest;
use ProgramerZamanNow\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use ProgramerZamanNow\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use ProgramerZamanNow\Belajar\PHP\MVC\Model\UserRegisterRequest;
use ProgramerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgramerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgramerZamanNow\Belajar\PHP\MVC\Service\SessionService;
use ProgramerZamanNow\Belajar\PHP\MVC\Service\UserService;

class UserController
{
    private UserService $userService;
    private SessionService $sessionService;
    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);
        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }
    public function register()
    {
        View::render('User/register', [
            'title' => 'Register new User',
        ]);
    }
    public function postRegister()
    {
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];
        try {
            $this->userService->register($request);
            /**
             * saat uji coba UserControllerTest ubah View::redirect('/users/login');
             */
            View::redirect('http://localhost/php-login-management/public/users/login');
            //View::redirect('/users/login');
        } catch (ValidationException $exception) {
            View::render('User/register', [
                'title' => 'Register new User',
                'error' => $exception->getMessage()
            ]);
        }
    }
    public function login()
    {
        View::render('User/login', [
            'title' => 'Login user',
        ]);
    }
    public function postLogin()
    {
        $request = new UserLoginRequest();
        $request->id = $_POST['id'];
        $request->password = $_POST['password'];
        try {
            $response = $this->userService->login($request);
            $this->sessionService->create($response->user->id);
            /**
             * saat uji coba UserControllerTest ubah View::redirect('/public/index');
             */
            View::redirect('http://localhost/php-login-management/public');
            //View::redirect('/public/index');
        } catch (ValidationException $exception) {
            View::render('User/login', [
                'title' => 'Login new User',
                'error' => $exception->getMessage()
            ]);
        }
    }
    public function logout()
    {
        $this->sessionService->destroy();
        View::redirect('http://localhost/php-login-management/public');
        //View::redirect('/public/index');
        /**
         * saat uji coba UserControllerTest ubah View::redirect('/public/index');
         */
    }
    public function updateProfile()
    {
        $user = $this->sessionService->current();
        View::render('User/profile', [
            "title" => "Update user profile",
            "user" => ["id" => $user->id, "name" => $user->name]
        ]);
    }
    public function postUpdateProfile()
    {
        $user = $this->sessionService->current();
        $request = new UserProfileUpdateRequest();
        $request->id = $user->id;
        $request->name = $_POST['name'];
        /**
         * saat uji coba UserControllerTest ubah View::redirect('/public/index');
         */
        try {
            $this->userService->updateProfile($request);
            View::redirect('http://localhost/php-login-management/public');
            //View::redirect('/public/index');
        } catch (ValidationException $exception) {
            View::render('User/profile', [
                "title" => "Update user profile",
                "error" => $exception->getMessage(),
                "user" => ["id" => $user->id, "name" => $_POST['name']]
            ]);
        }
    }
    public function updatePassword()
    {
        $user = $this->sessionService->current();
        View::render(
            'User/password',
            [
                "title" => "Update user password",
                "user" => ["id" => $user->id]
            ]
        );
    }
    public function postUpdatePassword()
    {
        $user = $this->sessionService->current();
        $request = new UserPasswordUpdateRequest();
        $request->id = $user->id;
        $request->oldPassword = $_POST['oldPassword'];
        $request->newPassword = $_POST['newPassword'];

        try {
            $this->userService->updatePassword($request);
            View::redirect('http://localhost/php-login-management/public');
            //View::redirect('/public/index');
        } catch (ValidationException $exception) {
            View::render(
                'User/password',
                [
                    "title" => "Update user password",
                    "error" => $exception->getMessage(),
                    "user" => ["id" => $user->id]
                ]
            );
        }
    }
}
