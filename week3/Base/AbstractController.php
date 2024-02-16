<?php 
namespace Base; 

use App\Model\User; 

class AbstractController 
{ 
    protected $view; 
    protected $session; 

    // Установка объекта View
    public function setView(View $view) 
    { 
        $this->view = $view; 
    } 

    // Получение пользователя или null, если не авторизован
    public function getUser(): ?User 
    { 
        $userId = $this->session->getUserId(); 
        if (!$userId) { 
            return null; 
        } 

        $user = User::getById($userId); 
        if (!$user) { 
            return null; 
        } 

        return $user; 
    } 

    // Получение идентификатора пользователя или false, если не авторизован
    public function getUserId() 
    { 
        if ($user = $this->getUser()) { 
            return $user->getId(); 
        } 

        return false; 
    } 

    // Установка объекта Session
    public function setSession(Session $session) 
    { 
        $this->session = $session; 
    } 

    // Вызов исключения для перенаправления на указанный URL
    public function redirect(string $url) 
    { 
        throw new RedirectException($url); 
    } 

    // Перед выполнением действия (диспетчера)
    public function preDispatch() 
    { 

    } 
}
