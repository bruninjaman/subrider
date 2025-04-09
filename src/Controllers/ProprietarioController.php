<?php

namespace App\Controllers;

use App\Repositories\ProprietarioRepository;

class ProprietarioController {
    private $proprietarioRepo;

    public function __construct() {
        $this->proprietarioRepo = new ProprietarioRepository();
    }

    /**
     * Cria um novo proprietário
     * 
     * @param array $dados Dados do proprietário
     * @return bool True se o proprietário foi criado com sucesso, false caso contrário
     */
    public function criar(array $dados): bool {
        return $this->proprietarioRepo->criar($dados);
    }

    /**
     * Valida os dados do proprietário
     * 
     * @param array $dados Dados do proprietário
     * @return array Array com erros de validação, vazio se não houver erros
     */
    public function validarDados(array $dados): array {
        $erros = [];

        if (empty($dados['nome'])) {
            $erros[] = 'Nome é obrigatório';
        }

        if (empty($dados['cpf'])) {
            $erros[] = 'CPF é obrigatório';
        } elseif (!$this->validarCPF($dados['cpf'])) {
            $erros[] = 'CPF inválido';
        }

        if (empty($dados['telefone'])) {
            $erros[] = 'Telefone é obrigatório';
        }

        if (empty($dados['endereco'])) {
            $erros[] = 'Endereço é obrigatório';
        }

        if (empty($dados['cidade'])) {
            $erros[] = 'Cidade é obrigatória';
        }

        if (empty($dados['estado'])) {
            $erros[] = 'Estado é obrigatório';
        }

        if (empty($dados['cep'])) {
            $erros[] = 'CEP é obrigatório';
        }

        return $erros;
    }

    /**
     * Valida um CPF
     * 
     * @param string $cpf CPF a ser validado
     * @return bool True se o CPF é válido, false caso contrário
     */
    private function validarCPF(string $cpf): bool {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        if (strlen($cpf) != 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }
} 