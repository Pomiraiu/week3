<?php

namespace App\Model;

use Base\Db;

class User
{
    private $id;
    private $name;
    private $Date;
    private $password;
    private $email;

    /**
     * Конструктор класса, инициализирует объект пользователя из переданных данных.
     */
    public function __construct(array $userData)
    {
        $this->name = $userData['name'];
        $this->password = $userData['password'];
        $this->Date = $userData['date'];
        $this->email = $userData['email'];
    }

    /**
     * Статический метод для получения пользователя по email.
     */
    public static function getByEmail(string $email): ?self
    {
        $db = Db::getInstance();
        $userData = $db->fetchOne(
            "SELECT * FROM users WHERE email = :email",
            METHOD,
            [':email' => $email]
        );
        if (!$userData) {
            return null;
        }

        $user = new self($userData);
        $user->id = $userData['id'];
        return $user;
    }

    
    public static function getByIds(array $userIds): array
    {
        $db = Db::getInstance();
        $idsString = implode(',', $userIds);
        $userData = $db->fetchAll(
            "SELECT * FROM users WHERE id IN ($idsString)",
            METHOD
        );
        if (!$userData) {
            return [];
        }

        $users = [];
        foreach ($userData as $userElem) {
            $user = new self($userElem);
            $user->id = $userElem['id'];
            $users[$user->id] = $user;
        }

        return $users;
    }

    /**
     * Метод для сохранения пользователя в базе данных.
     */
    public function save()
    {
        $db = Db::getInstance();
        $res = $db->exec(
            'INSERT INTO users (
                    name,  
                    password,  
                    date,
                    email
                    ) VALUES (
                    :name,  
                    :password,  
                    :date,
                    :email
                )',
            FILE,
            [
                ':name' => $this->name,
                ':password' => self::getPasswordHash($this->password),
                ':date' => $this->Date,
                ':email' => $this->email,
            ]
        );

        $this->id = $db->lastInsertId();

        return $res;
    }

    /**
     * Статический метод для получения пользователя по его ID.
     */
    public static function getById(int $userId): ?self
    {
        $db = Db::getInstance();
        $userData = $db->fetchOne(
            "SELECT * FROM users WHERE id = :id",
            METHOD,
            [':id' => $userId]
        );
        if (!$userData) {
            return null;
        }

        $user = new self($userData);
        $user->id = $userId;
        return $user;
    }

    /**
     * Статический метод для получения списка пользователей с определенным лимитом и смещением.
     */
    public static function getList(int $limit = 10, int $offset = 0): array
    {
        $db = Db::getInstance();
        $userData = $db->fetchAll(
            "SELECT * FROM users LIMIT $limit OFFSET $offset",
            METHOD
        );
        if (!$userData) {
            return [];
        }

        $users = [];
        foreach ($userData as $userElem) {
            $user = new self($userElem);
            $user->id = $userElem['id'];
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Статический метод для получения хэша пароля.
     */
    public static function getPasswordHash(string $password)
    {
        return sha1('fifire' . $password);
    }

    /**
     * Получение ID пользователя.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Получение имени пользователя.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Получение хэша пароля пользователя.
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Проверка, является ли пользователь администратором.
     */
    public function isAdmin(): bool
    {
    
        return in_array($this->id, ADMIN_IDS);
    }
}
