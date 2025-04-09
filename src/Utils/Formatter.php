<?php

namespace App\Utils;

/**
 * Classe utilitária para formatação de dados.
 */
class Formatter {

    /**
     * Formata uma data no padrão brasileiro.
     * 
     * @param string|null $data Data no formato Y-m-d ou similar.
     * @return string|null Data no formato d/m/Y ou null se a entrada for inválida.
     */
    public static function formataData(?string $data): ?string {
        if (empty($data)) {
            return null;
        }
        try {
            $dateTime = new \DateTime($data);
            return $dateTime->format('d/m/Y');
        } catch (\Exception $e) {
            // Logar erro ou retornar null/string vazia dependendo do requisito
            error_log("Erro ao formatar data: " . $e->getMessage());
            return null; 
        }
    }

    /**
     * Formata um valor monetário para Real (BRL).
     * 
     * @param float|null $valor Valor a ser formatado.
     * @return string Valor formatado como R$ 1.234,56 ou R$ 0,00 se nulo.
     */
    public static function formataValor(?float $valor): string {
        $valor = $valor ?? 0.0;
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }

    /**
     * Formata um CPF.
     * 
     * @param string|null $cpf CPF (apenas números).
     * @return string|null CPF formatado (XXX.XXX.XXX-XX) ou null se inválido.
     */
    public static function formataCPF(?string $cpf): ?string {
        $cpf = preg_replace('/[^0-9]/', '', $cpf ?? '');
        if (strlen($cpf) !== 11) {
            return null; // Ou retornar $cpf original? Depende do requisito.
        }
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }

    /**
     * Formata um CNPJ.
     * 
     * @param string|null $cnpj CNPJ (apenas números).
     * @return string|null CNPJ formatado (XX.XXX.XXX/XXXX-XX) ou null se inválido.
     */
    public static function formataCNPJ(?string $cnpj): ?string {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj ?? '');
        if (strlen($cnpj) !== 14) {
             return null; // Ou retornar $cnpj original? Depende do requisito.
        }
        return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
    }

    /**
     * Formata um número de telefone (fixo ou celular).
     * 
     * @param string|null $telefone Telefone (apenas números).
     * @return string|null Telefone formatado (XX) XXXXX-XXXX ou (XX) XXXX-XXXX, ou null se inválido.
     */
    public static function formataTelefone(?string $telefone): ?string {
        $telefone = preg_replace('/[^0-9]/', '', $telefone ?? '');
        $len = strlen($telefone);
        
        if ($len === 11) {
            return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7);
        } else if ($len === 10) {
            return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 4) . '-' . substr($telefone, 6);
        }
        
        return null; // Retorna null para comprimentos inválidos
    }

    /**
     * Formata um CEP.
     * 
     * @param string|null $cep CEP (apenas números).
     * @return string|null CEP formatado (XXXXX-XXX) ou null se inválido.
     */
    public static function formataCEP(?string $cep): ?string {
        $cep = preg_replace('/[^0-9]/', '', $cep ?? '');
         if (strlen($cep) !== 8) {
             return null; // Ou retornar $cep original? Depende do requisito.
        }
        return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
    }
} 