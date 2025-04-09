<?php

namespace App\Controllers;

use App\Repositories\OrdemServicoRepository;
use App\Repositories\MotoRepository;

class OrdemServicoController {
    private $ordemRepo;
    private $motoRepo;

    public function __construct() {
        $this->ordemRepo = new OrdemServicoRepository();
        $this->motoRepo = new MotoRepository();
    }

    /**
     * Cria uma nova ordem de serviço
     * 
     * @param array $dados Dados da ordem de serviço
     * @return array Array com resultado da operação
     */
    public function criar(array $dados): array {
        $erros = $this->validarDados($dados);
        
        if (!empty($erros)) {
            return [
                'sucesso' => false,
                'erros' => $erros
            ];
        }

        try {
            $ordemId = $this->ordemRepo->criar($dados);
            
            return [
                'sucesso' => true,
                'ordem_id' => $ordemId
            ];
        } catch (\Exception $e) {
            error_log("Erro ao criar ordem de serviço: " . $e->getMessage());
            
            return [
                'sucesso' => false,
                'erros' => ['Erro ao criar ordem de serviço. Por favor, tente novamente.']
            ];
        }
    }

    /**
     * Busca a lista de motos para o select
     * 
     * @return array Lista de motos
     */
    public function listarMotos(): array {
        return $this->motoRepo->listarComProprietarios();
    }

    /**
     * Valida os dados da ordem de serviço
     * 
     * @param array $dados Dados a serem validados
     * @return array Array com erros de validação, vazio se não houver erros
     */
    private function validarDados(array $dados): array {
        $erros = [];

        if (empty($dados['id_moto'])) {
            $erros[] = "Por favor, selecione uma moto";
        }

        if (empty($dados['data_entrada'])) {
            $erros[] = "Por favor, informe a data de entrada";
        }

        if (!empty($dados['km']) && !is_numeric($dados['km'])) {
            $erros[] = "A quilometragem deve ser um número";
        }

        return $erros;
    }
} 