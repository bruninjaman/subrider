<?php
namespace Tests\Mocks;

class SessionManager
{
    private static $instance = null;
    private $sessionData = [];

    private function __construct() {}

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function set($key, $value)
    {
        $this->sessionData[$key] = $value;
    }

    public function get($key)
    {
        return $this->sessionData[$key] ?? null;
    }

    public function destroy()
    {
        $this->sessionData = [];
    }

    public function isLoggedIn()
    {
        return isset($this->sessionData['user_id']);
    }
}