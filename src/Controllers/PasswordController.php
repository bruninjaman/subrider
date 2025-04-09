<?php

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Security\PasswordPolicy;

class PasswordController {
    private $userRepo;
    private $passwordPolicy;

    public function __construct() {
        $this->userRepo = new UserRepository();
        $this->passwordPolicy = new PasswordPolicy();
    }

    /**
     * Altera a senha do usuário
     * 
     * @param string $currentPassword Senha atual
     * @param string $newPassword Nova senha
     * @param string $confirmPassword Confirmação da nova senha
     * @return array Array com erros de validação, vazio se não houver erros
     */
    public function alterarSenha(string $currentPassword, string $newPassword, string $confirmPassword): array {
        $erros = [];

        // Verifica se as senhas novas são iguais
        if ($newPassword !== $confirmPassword) {
            $erros[] = "As senhas não coincidem";
            return $erros;
        }

        // Valida a senha atual
        if (!$this->userRepo->verificarSenha($_SESSION['user'], $currentPassword)) {
            $erros[] = "Senha atual incorreta";
            return $erros;
        }

        // Valida a nova senha
        $validacao = $this->passwordPolicy->validarSenha($newPassword);
        if (!$validacao['valido']) {
            $erros = array_merge($erros, $validacao['erros']);
        }

        // Verifica se a senha já foi usada
        if ($this->passwordPolicy->senhaJaUsada($_SESSION['user'], $newPassword)) {
            $erros[] = "Esta senha já foi utilizada recentemente";
        }

        // Se não houver erros, atualiza a senha
        if (empty($erros)) {
            if (!$this->userRepo->atualizarSenha($_SESSION['user'], $newPassword)) {
                $erros[] = "Erro ao atualizar a senha";
            } else {
                // Registra a nova senha no histórico
                $this->passwordPolicy->registrarHistoricoSenha($_SESSION['user'], $newPassword);
                
                // Remove flag de alteração forçada
                unset($_SESSION['force_password_change']);
            }
        }

        return $erros;
    }
} 