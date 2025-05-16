<?php
/**
 * Função para buscar imagem de peça com base nos dados disponíveis
 * 
 * Esta função procura imagens associadas às peças em várias fontes:
 * 1. No banco de dados na tabela pecas
 * 2. Em arquivos locais
 * 3. Retorna imagem padrão se não encontrar
 * 
 * @param string $identificador Código, ID ou nome da peça
 * @return string URL da imagem
 */
function buscarImagemPeca($identificador) {
    global $baseAddress, $conn;
    
    // Se o identificador estiver vazio, retorna imagem padrão
    if (empty($identificador) || $identificador == '0') {
        return $baseAddress . '/assets/css/images/peca-padrao.png';
    }
    
    // Sanitizar o identificador para evitar problemas de segurança
    $identificador_limpo = preg_replace('/[^a-zA-Z0-9-]/', '', $identificador);
    
    // Se tiver conexão com o banco, tenta buscar no banco de dados
    if ($conn) {
        // Opção 1: Buscar pelo pecaId
        $query = "SELECT foto FROM pecas WHERE pecaId = '$identificador_limpo' LIMIT 1";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (!empty($row['foto'])) {
                return construirUrlImagem($row['foto']);
            }
        }
        
        // Opção 2: Buscar pelo item ou parte que corresponda ao identificador
        $query = "SELECT foto FROM pecas WHERE 
                  item LIKE '%$identificador%' OR 
                  parte LIKE '%$identificador%' OR 
                  grupo LIKE '%$identificador%' 
                  LIMIT 1";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (!empty($row['foto'])) {
                return construirUrlImagem($row['foto']);
            }
        }
        
        // Opção 3: Tentar buscar no banco pela categoria específica da peça (caso seja um nome genérico)
        $query = "SELECT p.foto 
                 FROM pecas p 
                 INNER JOIN item_ordem i ON p.item = i.Item OR p.parte = i.Parte OR p.grupo = i.Grupo
                 WHERE i.Codigo = '$identificador_limpo' 
                 LIMIT 1";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (!empty($row['foto'])) {
                return construirUrlImagem($row['foto']);
            }
        }
    }
    
    // Se não encontrou no banco de dados, procura em arquivos locais
    $diretorio_imagens = __DIR__ . '/../assets/imagens/pecas/';
    
    // Verificar se existe uma imagem específica para este identificador
    $possiveis_arquivos = [
        $diretorio_imagens . $identificador_limpo . '.jpg',
        $diretorio_imagens . $identificador_limpo . '.png',
        $diretorio_imagens . $identificador_limpo . '.jpeg'
    ];
    
    // Verificar se algum dos arquivos possíveis existe
    foreach ($possiveis_arquivos as $arquivo) {
        if (file_exists($arquivo)) {
            // Converter para URL web
            $caminho_relativo = str_replace(__DIR__ . '/../', '', $arquivo);
            return $baseAddress . '/' . $caminho_relativo;
        }
    }
    
    // Se nada for encontrado, retorna a imagem padrão
    return $baseAddress . '/assets/css/images/peca-padrao.png';
}

/**
 * Função auxiliar para construir URL completa da imagem
 * 
 * @param string $caminho_imagem Caminho da imagem (pode ser relativo ou absoluto)
 * @return string URL completa da imagem
 */
function construirUrlImagem($caminho_imagem) {
    global $baseAddress;
    
    // Se o caminho estiver vazio, retorna imagem padrão
    if (empty($caminho_imagem)) {
        return $baseAddress . '/assets/css/images/peca-padrao.png';
    }
    
    // Se o caminho já for uma URL completa, use-o como está
    if (strpos($caminho_imagem, 'http://') === 0 || strpos($caminho_imagem, 'https://') === 0) {
        return $caminho_imagem;
    }
    
    // Caso contrário, converta para URL completa
    return $baseAddress . '/' . $caminho_imagem;
}
?> 