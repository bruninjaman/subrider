<?php
/**
 * Verifica se o usuário está logado
 * Se não estiver, redireciona para a página de login
 */
function verificaLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /login.php');
        exit;
    }
}

/**
 * Formata uma data no padrão brasileiro
 * 
 * @param string $data Data no formato Y-m-d
 * @return string Data no formato d/m/Y
 */
// REMOVED formataData function

/**
 * Formata um valor monetário
 * 
 * @param float $valor Valor a ser formatado
 * @return string Valor formatado com R$
 */
// REMOVED formataValor function

/**
 * Limpa uma string para uso em SQL
 * 
 * @param string $str String a ser limpa
 * @return string String limpa
 */
function limpaString($str) {
    return preg_replace('/[^a-zA-Z0-9\s]/', '', $str);
}

/**
 * Gera um slug a partir de uma string
 * 
 * @param string $str String para gerar o slug
 * @return string Slug gerado
 */
function geraSlug($str) {
    $str = strtolower(trim($str));
    $str = preg_replace('/[^a-z0-9-]/', '-', $str);
    $str = preg_replace('/-+/', '-', $str);
    return trim($str, '-');
}

/**
 * Valida uma placa de moto no formato Mercosul
 * 
 * @param string $placa Placa a ser validada
 * @return bool True se a placa é válida, false caso contrário
 */
function validaPlaca($placa) {
    return preg_match('/^[A-Z]{3}[0-9][A-Z][0-9]{2}$/', $placa);
}

/**
 * Valida um CPF
 * 
 * @param string $cpf CPF a ser validado
 * @return bool True se o CPF é válido, false caso contrário
 */
function validaCPF($cpf) {
    // Remove caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    // Verifica se tem 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }
    
    // Verifica se todos os dígitos são iguais
    if (preg_match('/^(\d)\1+$/', $cpf)) {
        return false;
    }
    
    // Calcula primeiro dígito verificador
    $soma = 0;
    for ($i = 0; $i < 9; $i++) {
        $soma += $cpf[$i] * (10 - $i);
    }
    $resto = $soma % 11;
    $dv1 = ($resto < 2) ? 0 : 11 - $resto;
    
    // Calcula segundo dígito verificador
    $soma = 0;
    for ($i = 0; $i < 10; $i++) {
        $soma += $cpf[$i] * (11 - $i);
    }
    $resto = $soma % 11;
    $dv2 = ($resto < 2) ? 0 : 11 - $resto;
    
    // Verifica se os dígitos verificadores estão corretos
    return ($cpf[9] == $dv1 && $cpf[10] == $dv2);
}

/**
 * Valida um CNPJ
 * 
 * @param string $cnpj CNPJ a ser validado
 * @return bool True se o CNPJ é válido, false caso contrário
 */
function validaCNPJ($cnpj) {
    // Remove caracteres não numéricos
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    
    // Verifica se tem 14 dígitos
    if (strlen($cnpj) != 14) {
        return false;
    }
    
    // Verifica se todos os dígitos são iguais
    if (preg_match('/^(\d)\1+$/', $cnpj)) {
        return false;
    }
    
    // Calcula primeiro dígito verificador
    $soma = 0;
    $multiplicador = 5;
    for ($i = 0; $i < 12; $i++) {
        $soma += $cnpj[$i] * $multiplicador;
        $multiplicador = ($multiplicador == 2) ? 9 : $multiplicador - 1;
    }
    $resto = $soma % 11;
    $dv1 = ($resto < 2) ? 0 : 11 - $resto;
    
    // Calcula segundo dígito verificador
    $soma = 0;
    $multiplicador = 6;
    for ($i = 0; $i < 13; $i++) {
        $soma += $cnpj[$i] * $multiplicador;
        $multiplicador = ($multiplicador == 2) ? 9 : $multiplicador - 1;
    }
    $resto = $soma % 11;
    $dv2 = ($resto < 2) ? 0 : 11 - $resto;
    
    // Verifica se os dígitos verificadores estão corretos
    return ($cnpj[12] == $dv1 && $cnpj[13] == $dv2);
}

/**
 * Formata um CPF
 * 
 * @param string $cpf CPF a ser formatado
 * @return string CPF formatado
 */
// REMOVED formataCPF function

/**
 * Formata um CNPJ
 * 
 * @param string $cnpj CNPJ a ser formatado
 * @return string CNPJ formatado
 */
// REMOVED formataCNPJ function

/**
 * Formata um telefone
 * 
 * @param string $telefone Telefone a ser formatado
 * @return string Telefone formatado
 */
// REMOVED formataTelefone function

/**
 * Formata um CEP
 * 
 * @param string $cep CEP a ser formatado
 * @return string CEP formatado
 */
// REMOVED formataCEP function

/**
 * Gera um token aleatório
 * 
 * @param int $tamanho Tamanho do token
 * @return string Token gerado
 */
function geraToken($tamanho = 32) {
    return bin2hex(random_bytes($tamanho));
}

/**
 * Envia um e-mail
 * 
 * @param string $para E-mail do destinatário
 * @param string $assunto Assunto do e-mail
 * @param string $mensagem Mensagem do e-mail
 * @return bool True se o e-mail foi enviado, false caso contrário
 */
function enviaEmail($para, $assunto, $mensagem) {
    $headers = "From: SubRider <noreply@subrider.com.br>\r\n";
    $headers .= "Reply-To: noreply@subrider.com.br\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($para, $assunto, $mensagem, $headers);
}

/**
 * Gera uma senha aleatória
 * 
 * @param int $tamanho Tamanho da senha
 * @return string Senha gerada
 */
function geraSenha($tamanho = 8) {
    $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+';
    $senha = '';
    
    for ($i = 0; $i < $tamanho; $i++) {
        $senha .= $caracteres[random_int(0, strlen($caracteres) - 1)];
    }
    
    return $senha;
}

/**
 * Verifica se uma string está em UTF-8
 * 
 * @param string $str String a ser verificada
 * @return bool True se a string está em UTF-8, false caso contrário
 */
function isUTF8($str) {
    return mb_check_encoding($str, 'UTF-8');
}

/**
 * Converte uma string para UTF-8
 * 
 * @param string $str String a ser convertida
 * @return string String convertida
 */
function converteUTF8($str) {
    if (!isUTF8($str)) {
        return utf8_encode($str);
    }
    return $str;
}

/**
 * Remove acentos de uma string
 * 
 * @param string $str String a ser limpa
 * @return string String sem acentos
 */
function removeAcentos($str) {
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"),explode(" ","a A e E i I o O u U n N c C"),$str);
}

/**
 * Limpa o nome de um arquivo
 * 
 * @param string $nome Nome do arquivo
 * @return string Nome limpo
 */
function limpaNomeArquivo($nome) {
    // Remove acentos
    $nome = removeAcentos($nome);
    
    // Remove caracteres especiais
    $nome = preg_replace('/[^a-zA-Z0-9\.]/', '_', $nome);
    
    // Remove underscores duplicados
    $nome = preg_replace('/_+/', '_', $nome);
    
    return $nome;
}

/**
 * Verifica se uma data é válida
 * 
 * @param string $data Data no formato Y-m-d
 * @return bool True se a data é válida, false caso contrário
 */
function validaData($data) {
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return $d && $d->format('Y-m-d') === $data;
}

/**
 * Calcula a diferença entre duas datas em dias
 * 
 * @param string $data1 Primeira data
 * @param string $data2 Segunda data
 * @return int Diferença em dias
 */
function diferencaDias($data1, $data2) {
    $d1 = new DateTime($data1);
    $d2 = new DateTime($data2);
    $diff = $d1->diff($d2);
    return $diff->days;
}

/**
 * Verifica se um arquivo é uma imagem
 * 
 * @param string $arquivo Caminho do arquivo
 * @return bool True se é uma imagem, false caso contrário
 */
function isImagem($arquivo) {
    $tipos = ['image/jpeg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tipo = finfo_file($finfo, $arquivo);
    finfo_close($finfo);
    return in_array($tipo, $tipos);
}

/**
 * Redimensiona uma imagem mantendo a proporção
 * 
 * @param string $arquivo Caminho do arquivo
 * @param int $largura Largura máxima
 * @param int $altura Altura máxima
 * @return bool True se a imagem foi redimensionada, false caso contrário
 */
function redimensionaImagem($arquivo, $largura, $altura) {
    list($width, $height) = getimagesize($arquivo);
    
    if ($width <= $largura && $height <= $altura) {
        return true;
    }
    
    $ratio = min($largura / $width, $altura / $height);
    $new_width = round($width * $ratio);
    $new_height = round($height * $ratio);
    
    $src = imagecreatefromstring(file_get_contents($arquivo));
    $dst = imagecreatetruecolor($new_width, $new_height);
    
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    $info = pathinfo($arquivo);
    $ext = strtolower($info['extension']);
    
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            return imagejpeg($dst, $arquivo, 90);
        case 'png':
            return imagepng($dst, $arquivo, 9);
        case 'gif':
            return imagegif($dst, $arquivo);
    }
    
    return false;
}

/**
 * Gera um thumbnail de uma imagem
 * 
 * @param string $arquivo Caminho do arquivo
 * @param string $destino Caminho do thumbnail
 * @param int $largura Largura do thumbnail
 * @param int $altura Altura do thumbnail
 * @return bool True se o thumbnail foi gerado, false caso contrário
 */
function geraThumbnail($arquivo, $destino, $largura, $altura) {
    list($width, $height) = getimagesize($arquivo);
    
    $ratio = min($largura / $width, $altura / $height);
    $new_width = round($width * $ratio);
    $new_height = round($height * $ratio);
    
    $src = imagecreatefromstring(file_get_contents($arquivo));
    $dst = imagecreatetruecolor($new_width, $new_height);
    
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    $info = pathinfo($destino);
    $ext = strtolower($info['extension']);
    
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            return imagejpeg($dst, $destino, 90);
        case 'png':
            return imagepng($dst, $destino, 9);
        case 'gif':
            return imagegif($dst, $destino);
    }
    
    return false;
}

/**
 * Verifica se um diretório existe e cria se não existir
 * 
 * @param string $dir Caminho do diretório
 * @return bool True se o diretório existe ou foi criado, false caso contrário
 */
function verificaDiretorio($dir) {
    if (!file_exists($dir)) {
        return mkdir($dir, 0777, true);
    }
    return true;
}

/**
 * Remove arquivos antigos de um diretório
 * 
 * @param string $dir Caminho do diretório
 * @param int $dias Número de dias
 * @return int Número de arquivos removidos
 */
function limpaArquivosAntigos($dir, $dias) {
    if (!is_dir($dir)) {
        return 0;
    }
    
    $limite = time() - ($dias * 24 * 60 * 60);
    $removidos = 0;
    
    foreach (new DirectoryIterator($dir) as $arquivo) {
        if ($arquivo->isDot()) continue;
        
        if ($arquivo->getMTime() < $limite) {
            unlink($arquivo->getPathname());
            $removidos++;
        }
    }
    
    return $removidos;
} 