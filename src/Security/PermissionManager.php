<?php
namespace Subrider\Security;

class PermissionManager {
    private static ?self $instance = null;
    private array $permissions = [];

    private function __construct() {}

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function loadUserPermissions(int $userId): void {
        // TODO: Carregar permissões do banco de dados
        $this->permissions = [
            'site.access' => true,
            'dashboard.view' => true
        ];
    }

    public function hasPermission(string $permission): bool {
        return $this->permissions[$permission] ?? false;
    }
} 