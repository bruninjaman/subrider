<?php
/**
 * Funções de validação para medições
 */

/**
 * Valida medições do cabeçote
 * @param array $data Dados a serem validados
 * @return array Array com erros encontrados
 */
function validateCabecote($data) {
    $errors = [];
    
    // Validações específicas do cabeçote
    if (!isset($data['engine_type']) || empty($data['engine_type'])) {
        $errors[] = "Tipo do motor é obrigatório";
    }
    
    if (!isset($data['valve_type']) || empty($data['valve_type'])) {
        $errors[] = "Tipo de válvula é obrigatório";
    }
    
    if (!isset($data['num_cilindros']) || !is_numeric($data['num_cilindros']) || $data['num_cilindros'] <= 0) {
        $errors[] = "Número de cilindros inválido";
    }
    
    return $errors;
}

/**
 * Valida medições da bomba
 * @param array $data Dados a serem validados
 * @return array Array com erros encontrados
 */
function validateBomba($data) {
    $errors = [];
    
    // Validações específicas da bomba
    if (!isset($data['pressao_oleo_min']) || !is_numeric($data['pressao_oleo_min'])) {
        $errors[] = "Pressão mínima do óleo inválida";
    }
    
    if (!isset($data['pressao_oleo_max']) || !is_numeric($data['pressao_oleo_max'])) {
        $errors[] = "Pressão máxima do óleo inválida";
    }
    
    if (isset($data['pressao_oleo_min']) && isset($data['pressao_oleo_max']) && 
        $data['pressao_oleo_min'] > $data['pressao_oleo_max']) {
        $errors[] = "Pressão mínima não pode ser maior que a máxima";
    }
    
    return $errors;
}

/**
 * Valida medições do motor
 * @param array $data Dados a serem validados
 * @return array Array com erros encontrados
 */
function validateMotor($data) {
    $errors = [];
    
    // Validações específicas do motor
    if (!isset($data['cilindrada']) || !is_numeric($data['cilindrada']) || $data['cilindrada'] <= 0) {
        $errors[] = "Cilindrada inválida";
    }
    
    if (!isset($data['compressao']) || !is_numeric($data['compressao']) || $data['compressao'] <= 0) {
        $errors[] = "Taxa de compressão inválida";
    }
    
    return $errors;
}

/**
 * Valida medições do virabrequim
 * @param array $data Dados a serem validados
 * @return array Array com erros encontrados
 */
function validateVirabrequim($data) {
    $errors = [];
    
    // Validações específicas do virabrequim
    if (!isset($data['curso']) || !is_numeric($data['curso']) || $data['curso'] <= 0) {
        $errors[] = "Curso inválido";
    }
    
    if (!isset($data['diametro']) || !is_numeric($data['diametro']) || $data['diametro'] <= 0) {
        $errors[] = "Diâmetro inválido";
    }
    
    return $errors;
}

/**
 * Valida medições da embreagem
 * @param array $data Dados a serem validados
 * @return array Array com erros encontrados
 */
function validateEmbreagem($data) {
    $errors = [];
    
    // Validações específicas da embreagem
    if (!isset($data['tipo']) || empty($data['tipo'])) {
        $errors[] = "Tipo de embreagem é obrigatório";
    }
    
    if (!isset($data['num_discos']) || !is_numeric($data['num_discos']) || $data['num_discos'] <= 0) {
        $errors[] = "Número de discos inválido";
    }
    
    return $errors;
}

/**
 * Valida todos os dados de medição
 * @param array $data Dados a serem validados
 * @param string $type Tipo de medição (cabecote, bomba, motor, virabrequim, embreagem)
 * @return array Array com erros encontrados
 */
function validateMedicoes($data, $type) {
    switch ($type) {
        case 'cabecote':
            return validateCabecote($data);
        case 'bomba':
            return validateBomba($data);
        case 'motor':
            return validateMotor($data);
        case 'virabrequim':
            return validateVirabrequim($data);
        case 'embreagem':
            return validateEmbreagem($data);
        default:
            return ["Tipo de medição inválido"];
    }
}