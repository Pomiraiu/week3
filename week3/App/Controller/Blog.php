<?php

namespace App\Controller;

use App\Model\Message;
use Base\AbstractController;

class Blog extends AbstractController
{
    /**
     * Отображение главной страницы блога с сообщениями пользователей.
     */
    public function index()
    {
        // Проверка наличия пользователя
        if (!$this->getUser()) {
            $this->redirect('/login');
        }

        // Получение списка сообщений
        $messages = Message::getList();

        // Если есть сообщения, получаем информацию о пользователях и связываем ее с сообщениями
        if ($messages) {
            $userIds = array_map(function (Message $message) {
                return $message->getAuthorId();
            }, $messages);

            $users = \App\Model\User::getByIds($userIds);

            array_walk($messages, function (Message $message) use ($users) {
                if (isset($users[$message->getAuthorId()])) {
                    $message->setAuthor($users[$message->getAuthorId()]);
                }
            });
        }

        // Отображение шаблона блога с данными
        return $this->view->render('blog.phtml', [
            'messages' => $messages,
            'user' => $this->getUser()
        ]);
    }

    /**
     * Добавление нового сообщения в блог.
     */
    public function addMessage()
    {
        // Проверка наличия пользователя
        if (!$this->getUser()) {
            $this->redirect('/login');
        }

        // Получение текста сообщения из POST-запроса
        $text = (string) $_POST['text'];

        // Проверка наличия текста
        if (!$text) {
            $this->error('Сообщение не может быть пустым');
        }

        // Создание нового объекта сообщения и сохранение его
        $message = new Message([
            'text' => $text,
            'author_id' => $this->getUserId(),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Загрузка изображения, если оно предоставлено
        if (isset($_FILES['image']['tmp_name'])) {
            $message->loadFile($_FILES['image']['tmp_name']);
        }

        // Сохранение сообщения и перенаправление на главную страницу блога
        $message->save();
        $this->redirect('/blog');
    }


    public function twig()
    {
        return $this->view->renderTwig('test.twig', ['var' => 'meow']);
    }


    private function error()
    {
        // Логика обработки ошибок
    }
}
