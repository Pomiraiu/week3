<?php

namespace App\Controller;

use App\Model\User;
use Base\AbstractController;

class Login extends AbstractController
{
    /**
     * Отображение страницы входа.
     */
    public function index()
    {
        // Проверка наличия пользователя
        if ($this->getUser()) {
            $this->redirect('/blog');
        }

        // Отображение шаблона входа с данными
        return $this->view->render(
            'login.phtml',
            [
                'title' => 'Главная',
                'user' => $this->getUser(),
            ]
        );
    }

    /**
     * Аутентификация пользователя.
     */
    public function auth()
    {
        $email = (string) $_POST['email'];
        $password = (string) $_POST['password'];

        // Получение пользователя по email
        $user = User::getByEmail($email);

        // Проверка наличия пользователя
        if (!$user) {
            return 'Неверный логин и пароль';
        }

        // Проверка совпадения пароля
        if ($user->getPassword() !== User::getPasswordHash($password)) {
            return 'Неверный логин и пароль';
        }

        // Аутентификация пользователя и перенаправление на главную страницу блога
        $this->session->authUser($user->getId());
        $this->redirect('/blog');
    }

    /**
     * Регистрация нового пользователя.
     */
    public function register()
    {
        $name = (string) $_POST['name'];
        $email = (string) $_POST['email'];
        $password = (string) $_POST['password'];
        $password2 = (string) $_POST['password_2'];

        // Проверка наличия имени и пароля
        if (!$name || !$password) {
            return 'Не заданы имя и пароль';
        }

        // Проверка наличия email
        if (!$email) {
            return 'Не задан email';
        }

        // Проверка совпадения паролей
        if ($password !== $password2) {
            return 'Введенные пароли не совпадают';
        }

        // Проверка длины пароля
        if (mb_strlen($password) < 5) {
            return 'Пароль слишком короткий';
        }

        // Данные нового пользователя
        $userData = [
            'name' => $name,
            'created_at' => date('Y-m-d H:i:s'),
            'password' => $password,
            'email' => $email,
        ];

        // Создание нового пользователя, сохранение его и аутентификация
        $user = new User($userData);
        $user->save();
        $this->session->authUser($user->getId());
        $this->redirect('/blog');
    }
}
