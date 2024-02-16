<?php

namespace App\Controller;

use App\Model\Message;
use Base\AbstractController;

class Api extends AbstractController
{
    /**
     * Получение сообщений пользователя по его ID.
     */
    public function getUserMessages()
    {
        $userId = (int) $_GET['user_id'] ?? 0;

        // Проверка наличия ID пользователя
        if (!$userId) {
            return $this->response(['error' => 'no_user_id']);
        }

        // Получение сообщений пользователя с ограничением в 20 записей
        $messages = Message::getUserMessages($userId, 20);

        // Проверка наличия сообщений
        if (!$messages) {
            return $this->response(['error' => 'no_messages']);
        }

        // Преобразование данных каждого сообщения для ответа
        $data = array_map(function (Message $message) {
            return $message->getData();
        }, $messages);

        return $this->response(['messages' => $data]);
    }

    /**
     * Отправка ответа в формате JSON.
     */
    public function response(array $data)
    {
        header('Content-type: application/json');

        // Преобразование массива данных в JSON и возврат
        return json_encode($data);
    }
}
