<?php

namespace Subrider\Security;

class Security {
    /**
     * Gera um token CSRF
     *
     * @return string
     */
    public static function generateCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verifica se um token CSRF é válido
     *
     * @param string $token
     * @return bool
     */
    public static function validateCsrfToken(?string $token): bool {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Sanitiza uma string para evitar XSS
     *
     * @param string $input
     * @return string
     */
    public static function sanitizeString(string $input): string {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Gera um hash seguro de senha
     *
     * @param string $password
     * @return string
     */
    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_ARGON2ID);
    }

    /**
     * Verifica se uma senha corresponde ao hash
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    /**
     * Gera um token de autenticação
     *
     * @return string
     */
    public static function generateAuthToken(): string {
        return bin2hex(random_bytes(32));
    }

    /**
     * Sanitiza um array de dados
     *
     * @param array $data
     * @return array
     */
    public static function sanitizeArray(array $data): array {
        return array_map(function($item) {
            if (is_array($item)) {
                return self::sanitizeArray($item);
            }
            return is_string($item) ? self::sanitizeString($item) : $item;
        }, $data);
    }

    /**
     * Valida e sanitiza um endereço de email
     *
     * @param string $email
     * @return string|false
     */
    public static function validateEmail(string $email) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
    }
} 