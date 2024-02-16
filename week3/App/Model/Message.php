<?php

namespace App\Model;

use Base\Db;

class Message
{
    private $id;
    private $text;
    private $date;
    private $authorId;
    private $author;
    private $image;

   
    public function __construct(array $data)
    {
        $this->text = $data['text'];
        $this->date = $data['date'];
        $this->authorId = $data['author_id'];
        $this->image = $data['image'] ?? '';
    }

    /**
     * Статический метод для удаления сообщения по его ID.
     */
    public static function deleteMessage(int $messageId)
    {
        $db = Db::getInstance();
        $query = "DELETE FROM messages WHERE id = $messageId";
        return $db->exec($query, __METHOD__);
    }

    /**
     * Метод для сохранения сообщения в базе данных.
     */
    public function save()
    {
        $db = Db::getInstance();
        $res = $db->exec(
            'INSERT INTO messages (
                    text,  
                    date,
                    author_id,
                    image
                    ) VALUES (
                    :text,  
                    :date,
                    :author_id,
                    :image
                )',
            FILE,
            [
                ':text' => $this->text,
                ':date' => $this->createdAt,
                ':author_id' => $this->authorId,
                ':image' => $this->image,
            ]
        );

        return $res;
    }

    /**
     * Статический метод для получения списка сообщений с определенным лимитом и смещением.
     */
    public static function getList(int $limit = 10, int $offset = 0): array
    {
        $db = Db::getInstance();
        $data = $db->fetchAll(
            "SELECT * FROM messages LIMIT $limit OFFSET $offset",
            METHOD
        );
        if (!$data) {
            return [];
        }

        $messages = [];
        foreach ($data as $messageData) {
            $message = new self($messageData);
            $message->id = $messageData['id'];
            $messages[] = $message;
        }

        return $messages;
    }

    /**
     * Статический метод для получения сообщений пользователя с определенным лимитом.
     */
    public static function getUserMessages(int $userId, int $limit): array
    {
        $db = Db::getInstance();
        $data = $db->fetchAll(
            "SELECT * FROM messages WHERE author_id = $userId LIMIT $limit",
            METHOD
        );
        if (!$data) {
            return [];
        }

        $userMessages = [];
        foreach ($data as $messageData) {
            $message = new self($messageData);
            $message->id = $messageData['id'];
            $userMessages[] = $message;
        }

        return $userMessages;
    }

    /**
     * Получение ID сообщения.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Получение текста сообщения.
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Получение времени создания сообщения.
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Получение ID автора сообщения.
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * Получение объекта пользователя-автора сообщения.
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * Установка объекта пользователя-автора сообщения.
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * Загрузка файла изображения.
     */
    public function loadFile(string $file)
    {
        if (file_exists($file)) {
            $this->image = $this->generateFileName();
            move_uploaded_file($file, getcwd() . '/images/' . $this->image);
        }
    }

    /**
     * Генерация уникального имени файла изображения.
     */
    private function generateFileName()
    {
        return sha1(microtime(1) . mt_rand(1, 100000000)) . '.jpg';
    }

    /**
     * Получение имени файла изображения.
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Получение данных сообщения в виде ассоциативного массива.
     */
    public function getData()
    {
        return [
            'id' => $this->id,
            'author_id' => $this->authorId,
            'text' => $this->text,
            'date' => $this->createdAt,
            'image' => $this->image,
        ];
    }
}
