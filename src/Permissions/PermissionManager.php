<?php
/**
 * Gerenciador de Permissões
 * 
 * Classe responsável por gerenciar as permissões de acesso no sistema
 * 
 * @package Subrider
 * @subpackage Permissions
 * @version 1.0.0
 */

namespace Subrider\Permissions;

class PermissionManager
{
    /**
     * @var array Lista de páginas públicas que não requerem autenticação
     */
    private static array $publicPages = [
        'login.php',
        'register.php',
        'forgot-password.php',
        'reset-password.php'
    ];

    /**
     * @var array Cache de permissões do usuário
     */
    private static array $permissionCache = [];

    /**
     * Verifica se a página atual é pública
     *
     * @param string $page Nome da página
     * @return bool
     */
    public static function isPublicPage(string $page): bool
    {
        return in_array($page, self::$publicPages);
    }

    /**
     * Verifica se o usuário está autenticado
     *
     * @return bool
     */
    public static function isAuthenticated(): bool
    {
        return isset($_SESSION["user"]) && isset($_SESSION["type"]);
    }

    /**
     * Verifica se o usuário tem o nível de permissão necessário
     *
     * @param int $requiredLevel Nível de permissão necessário
     * @return bool
     */
    public static function hasPermission(int $requiredLevel): bool
    {
        if (!self::isAuthenticated()) {
            return false;
        }

        return $_SESSION["type"] >= $requiredLevel;
    }

    /**
     * Verifica as permissões para a página atual
     *
     * @return void
     */
    public static function checkPermissions(): void
    {
        // Não verifica permissões durante o processo de login
        if (defined('IS_LOGIN_PROCESS') && IS_LOGIN_PROCESS === true) {
            return;
        }

        $currentPage = basename($_SERVER['PHP_SELF']);

        // Log para debugging
        \logDebug('Verificando permissões', [
            'page' => $currentPage,
            'session' => isset($_SESSION) ? array_keys($_SESSION) : [],
            'is_public' => self::isPublicPage($currentPage)
        ]);

        // Se não for página pública, verifica autenticação
        if (!self::isPublicPage($currentPage)) {
            if (!self::isAuthenticated()) {
                \logDebug('Usuário não logado, redirecionando para login');
                header("Location: /subrider/login.php");
                exit();
            }

            // Verifica se o usuário tem permissão adequada
            if (!self::hasPermission(PERMISSION_USER)) {
                \logDebug('Usuário sem permissão adequada', ['type' => $_SESSION["type"]]);
                self::logout();
                header("Location: /subrider/login.php?error=permission");
                exit();
            }

            // Log de acesso bem-sucedido
            \logDebug('Acesso permitido', [
                'user' => $_SESSION["user"],
                'type' => $_SESSION["type"],
                'page' => $currentPage
            ]);
        }
    }

    /**
     * Faz logout do usuário
     *
     * @return void
     */
    public static function logout(): void
    {
        // Limpa o cache de permissões
        self::$permissionCache = [];
        
        // Destrói a sessão
        session_destroy();
        
        // Log do logout
        \logDebug('Usuário fez logout', [
            'user' => $_SESSION["user"] ?? 'unknown',
            'type' => $_SESSION["type"] ?? 'unknown'
        ]);
    }

    /**
     * Adiciona uma página à lista de páginas públicas
     *
     * @param string $page Nome da página
     * @return void
     */
    public static function addPublicPage(string $page): void
    {
        if (!in_array($page, self::$publicPages)) {
            self::$publicPages[] = $page;
        }
    }

    /**
     * Remove uma página da lista de páginas públicas
     *
     * @param string $page Nome da página
     * @return void
     */
    public static function removePublicPage(string $page): void
    {
        $key = array_search($page, self::$publicPages);
        if ($key !== false) {
            unset(self::$publicPages[$key]);
            self::$publicPages = array_values(self::$publicPages);
        }
    }
} 