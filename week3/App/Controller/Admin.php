<?php

namespace App\Controller;

use App\Model\Message;
use Base\AbstractController;

class Admin extends AbstractController
{
    // Переопределение метода предварительной обработки
    public function preDispatch()
    {
        parent::preDispatch();

        // Проверка наличия пользователя и его прав 
        if (!$this->getUser() || !$this->getUser()->isAdmin()) {
            $this->redirect('/'); // Перенаправление на главную
        }
    }

    // Метод для удаления сообщения
    public function deleteMessage()
    {
        $messageId = (int) $_GET['id']; // Получение ID сообщения из параметра запроса
        Message::deleteMessage($messageId); // Вызов статического метода модели для удаления сообщения
        $this->redirect('/blog'); // Перенаправление на страницу блога после удаления
    }
}