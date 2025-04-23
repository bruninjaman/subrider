<?php
/**
 * Localizador de caminhos alternativos
 */

namespace PathTools\Lib;

use PathTools\Utils\FileUtils;
use PathTools\Utils\PathUtils;

class PathFinder {
    /**
     * Encontra um caminho alternativo para um arquivo
     * 
     * @param string $oldPath Caminho original
     * @param string $sourceFile Arquivo fonte para contexto
     * @return string Caminho alternativo encontrado
     */
    public static function findAlternativePath($oldPath, $sourceFile) {
        error_log("\n------ INICIANDO BUSCA POR CAMINHO ALTERNATIVO ------");
        error_log("Caminho original: $oldPath");
        error_log("Arquivo fonte: $sourceFile");
        
        // Verifica se o caminho contém código PHP
        $hasPhpCode = false;
        $staticPath = $oldPath;
        
        if (strpos($oldPath, '<?php') !== false || strpos($oldPath, '<?=') !== false) {
            $hasPhpCode = true;
            error_log("Caminho contém código PHP, extraindo parte estática");
            
            // Extrair parte estática do caminho (após o código PHP)
            preg_match('/(?:\?>|;)\s*\/(.+)$/', $oldPath, $matches);
            if (!empty($matches[1])) {
                $staticPath = $matches[1];
                error_log("Parte estática extraída: $staticPath");
            } else {
                // Tenta extrair o último segmento do caminho (após a última /)
                $parts = explode('/', $oldPath);
                $staticPath = end($parts);
                error_log("Último segmento do caminho: $staticPath");
            }
        }
        
        // Obter diretório absoluto do arquivo fonte
        $sourceDir = dirname(realpath($sourceFile));
        
        // Extrair o nome do arquivo do caminho antigo
        $filename = basename($staticPath);
        error_log("Nome do arquivo a procurar: $filename");
        
        // Raiz do projeto
        $projectRoot = realpath(PROJECT_ROOT);
        
        // Verificar se o arquivo fonte está sendo incluído por outro script
        $isIncluded = false;
        $includingScripts = IncludeAnalyzer::findIncludingScripts($sourceFile);
        
        if (!empty($includingScripts)) {
            error_log("O arquivo $sourceFile é incluído por outros scripts: " . implode(", ", $includingScripts));
            $isIncluded = true;
            
            // Verificar se estamos lidando com um arquivo PHP
            $isPhpFile = pathinfo($filename, PATHINFO_EXTENSION) == 'php';
            
            // ABORDAGEM 0: Primeiro tentar resolver em relação ao script inclusor
            foreach ($includingScripts as $includingScript) {
                $includingDir = dirname(realpath($includingScript));
                error_log("Verificando a partir do script inclusor: $includingScript");
                
                // Se o caminho começa com ../ remove os níveis necessários
                if (strpos($staticPath, '../') === 0) {
                    $adjustedBase = PathUtils::adjustBaseForRelativePath($includingDir, $staticPath);
                    $pathWithoutDots = preg_replace('/^(\.\.\/)+/', '', $staticPath);
                    $possiblePath = $adjustedBase . '/' . $pathWithoutDots;
                    error_log("Tentando caminho ajustado do inclusor: $possiblePath");
                    
                    if (file_exists($possiblePath)) {
                        // Se for arquivo PHP, fazer caminho relativo a partir do inclusor
                        if ($isPhpFile) {
                            $relativePath = FileUtils::makeRelativePath($includingDir, $possiblePath);
                            error_log("SUCESSO! Caminho relativo ao inclusor para PHP: $relativePath");
                            return $relativePath;
                        } else {
                            // Para outros tipos de arquivo, manter comportamento original
                            $relativePath = FileUtils::makeRelativePath($sourceDir, $possiblePath);
                            error_log("SUCESSO! Caminho encontrado a partir do script inclusor (ajustado): $relativePath");
                            return $relativePath;
                        }
                    }
                }
                
                // Tentar direto relativo ao inclusor
                $directPath = $includingDir . '/' . $staticPath;
                error_log("Tentando direto do inclusor: $directPath");
                
                if (file_exists($directPath)) {
                    // Se for arquivo PHP, fazer caminho relativo a partir do inclusor
                    if ($isPhpFile) {
                        $relativePath = FileUtils::makeRelativePath($includingDir, $directPath);
                        error_log("SUCESSO! Caminho relativo ao inclusor para PHP: $relativePath");
                        return $relativePath;
                    } else {
                        // Para outros tipos de arquivo, manter comportamento original
                        $relativePath = FileUtils::makeRelativePath($sourceDir, $directPath);
                        error_log("SUCESSO! Caminho encontrado a partir do script inclusor: $relativePath");
                        return $relativePath;
                    }
                }
                
                // Tentar com ./
                $dotPath = $includingDir . '/' . ltrim($staticPath, './');
                if ($dotPath !== $directPath) {
                    error_log("Tentando com ./ removido: $dotPath");
                    if (file_exists($dotPath)) {
                        // Se for arquivo PHP, fazer caminho relativo a partir do inclusor
                        if ($isPhpFile) {
                            $relativePath = FileUtils::makeRelativePath($includingDir, $dotPath);
                            error_log("SUCESSO! Caminho relativo ao inclusor para PHP: $relativePath");
                            return $relativePath;
                        } else {
                            // Para outros tipos de arquivo, manter comportamento original
                            $relativePath = FileUtils::makeRelativePath($sourceDir, $dotPath);
                            error_log("SUCESSO! Caminho encontrado com ./ removido: $relativePath");
                            return $relativePath;
                        }
                    }
                }
                
                // Tentar com ./assets no inclusor
                $commonDirs = ['', 'assets', 'css', 'js', 'img', 'images', 'includes'];
                foreach ($commonDirs as $dir) {
                    $includingCommonPath = $includingDir . '/' . ($dir ? "$dir/" : '') . $filename;
                    error_log("Tentando com diretório comum a partir do inclusor: $includingCommonPath");
                    
                    if (file_exists($includingCommonPath)) {
                        // Se for arquivo PHP, fazer caminho relativo a partir do inclusor
                        if ($isPhpFile) {
                            $relativePath = ($dir ? "$dir/" : '') . $filename;
                            error_log("SUCESSO! Caminho relativo ao inclusor para PHP (com diretório comum): $relativePath");
                            return $relativePath;
                        } else {
                            // Para outros tipos de arquivo, manter comportamento original
                            $relativePath = FileUtils::makeRelativePath($sourceDir, $includingCommonPath);
                            error_log("SUCESSO! Caminho encontrado em diretório comum do inclusor: $relativePath");
                            return $relativePath;
                        }
                    }
                }
            }
        }
        
        // ABORDAGEM 1: Verificar se o caminho existe com a estrutura atual
        // Tenta primeiro corrigir erros simples de capitalização ou pequenas diferenças
        $oldDirPath = dirname($staticPath);
        $oldDirParts = explode('/', $oldDirPath);
        
        // Tentar manter a estrutura original intacta
        $possiblePath = $staticPath;
        $absolutePath = $sourceDir . '/' . $possiblePath;
        error_log("Tentando caminho direto: $absolutePath");
        
        if (file_exists($absolutePath)) {
            error_log("Caminho original existe! Mantendo-o: $possiblePath");
            return $possiblePath;
        }
        
        // ABORDAGEM 2: Se tiver partes PHP e um diretório, tratar com especial cuidado
        if ($hasPhpCode && $oldDirPath !== '.' && $oldDirPath !== '/') {
            // Pega todos os componentes do caminho
            $allDirParts = explode('/', $oldPath);
            
            // Identifica a última parte (que deve ser o arquivo)
            $filenamePart = array_pop($allDirParts);
            
            // Verifica se o penúltimo componente contém código PHP
            $lastDirPart = array_pop($allDirParts);
            
            // Se o penúltimo componente não contém PHP, recoloca
            if (strpos($lastDirPart, '<?php') === false && strpos($lastDirPart, '<?=') === false) {
                array_push($allDirParts, $lastDirPart);
            }
            
            // Procura por diretórios que correspondem à última parte do caminho
            $parts = explode('/', $staticPath);
            if (count($parts) > 1) {
                $lastDir = $parts[count($parts) - 2]; // Penúltimo elemento é o diretório
                
                // Tentar com estrutura completa de diretório
                if (!empty($lastDir)) {
                    $searchPaths = [
                        "$lastDir/$filename",
                        "assets/$lastDir/$filename",
                        "css/$lastDir/$filename",
                        "js/$lastDir/$filename"
                    ];
                    
                    foreach ($searchPaths as $testPath) {
                        error_log("Tentando com estrutura de diretório preservada: $testPath");
                        $fullPath = $sourceDir . '/' . $testPath;
                        
                        if (file_exists($fullPath)) {
                            error_log("SUCESSO! Caminho encontrado com estrutura preservada: $testPath");
                            return $testPath;
                        }
                    }
                }
            }
        }
        
        // ABORDAGEM 3: Tentar encontrar o arquivo na mesma estrutura de diretórios
        // mas corrigindo possíveis erros de capitalização/naming
        $dirsToSearch = [$sourceDir]; // Iniciar com o diretório do arquivo fonte
        
        // Adicionar mais lugares para procurar
        if (!empty($oldDirParts) && count($oldDirParts) > 0) {
            // Tentar em subdiretórios comuns com a mesma estrutura
            $commonParentDirs = ['', 'assets', 'images', 'img', 'css', 'js', 'includes', 'media'];
            foreach ($commonParentDirs as $parentDir) {
                if (!empty($parentDir)) {
                    $dirsToSearch[] = $sourceDir . '/' . $parentDir;
                }
            }
        }
        
        error_log("Procurando em " . count($dirsToSearch) . " diretórios, preservando estrutura");
        foreach ($dirsToSearch as $baseDir) {
            // Recriar a estrutura de pastas, mas a partir do diretório base atual
            $pathToTry = '';
            if (count($oldDirParts) > 0 && $oldDirParts[0] !== '.') {
                $pathToTry = implode('/', $oldDirParts);
                $fullDirPath = $baseDir . '/' . $pathToTry;
                $fullFilePath = $fullDirPath . '/' . $filename;
                
                error_log("Tentando: $fullFilePath");
                if (file_exists($fullFilePath)) {
                    $relativePath = FileUtils::makeRelativePath($sourceDir, $fullFilePath);
                    error_log("SUCESSO! Encontrado mantendo estrutura: $relativePath");
                    return $relativePath;
                }
                
                // Tentar apenas pela última parte do diretório + filename
                if (count($oldDirParts) > 0) {
                    $lastDir = $oldDirParts[count($oldDirParts) - 1];
                    $partialPath = $lastDir . '/' . $filename;
                    $fullDirPath = $baseDir . '/' . $lastDir;
                    $fullFilePath = $fullDirPath . '/' . $filename;
                    
                    error_log("Tentando com última pasta: $fullFilePath");
                    if (file_exists($fullFilePath)) {
                        $relativePath = FileUtils::makeRelativePath($sourceDir, $fullFilePath);
                        error_log("SUCESSO! Encontrado com última pasta: $relativePath");
                        return $relativePath;
                    }
                }
            }
        }
        
        // ABORDAGEM 4: Busca recursiva mais ampla para achar o arquivo em qualquer lugar
        error_log("Iniciando busca recursiva pelo arquivo: $filename");
        
        // Armazenar todos os caminhos encontrados
        $foundFiles = array();
        FileUtils::findFileRecursively($projectRoot, $filename, $foundFiles);
        
        if (!empty($foundFiles)) {
            error_log("Encontrado(s) " . count($foundFiles) . " arquivo(s) com o nome");
            
            // Se temos múltiplos arquivos encontrados, damos preferência aos que mantêm a estrutura original
            if (count($foundFiles) > 1 && !empty($oldDirParts) && count($oldDirParts) > 0) {
                $bestMatches = [];
                
                // Se temos um caminho com PHP, tentar identificar componentes da estrutura de diretórios
                if ($hasPhpCode) {
                    // Extrair quaisquer partes de diretório do caminho estático
                    preg_match('/([^\/]+\/)+/', $staticPath, $dirMatches);
                    $structureParts = [];
                    
                    if (!empty($dirMatches[0])) {
                        $structureParts = explode('/', rtrim($dirMatches[0], '/'));
                    }
                    
                    // Dar peso alto para arquivos que mantêm estrutura similar
                    foreach ($foundFiles as $foundFile) {
                        $foundParts = explode('/', $foundFile);
                        $matchCount = 0;
                        
                        foreach ($structureParts as $part) {
                            if (in_array($part, $foundParts)) {
                                $matchCount++;
                            }
                        }
                        
                        if ($matchCount > 0) {
                            $bestMatches[$foundFile] = $matchCount;
                        }
                    }
                    
                    // Ordenar por número de correspondências
                    if (!empty($bestMatches)) {
                        arsort($bestMatches);
                        $bestFile = array_key_first($bestMatches);
                        $relativePath = FileUtils::makeRelativePath($sourceDir, $bestFile);
                        error_log("Melhor correspondência estrutural: $relativePath");
                        return $relativePath;
                    }
                }
                
                // Verificar por correspondências de estrutura de diretórios
                $lastDir = end($oldDirParts);
                foreach ($foundFiles as $foundFile) {
                    $foundDirname = dirname($foundFile);
                    $foundDirParts = explode('/', $foundDirname);
                    
                    // Verificar se o último diretório do caminho original está presente
                    if (in_array($lastDir, $foundDirParts)) {
                        $relativePath = FileUtils::makeRelativePath($sourceDir, $foundFile);
                        error_log("Correspondência de estrutura encontrada: $relativePath");
                        return $relativePath;
                    }
                }
            }
            
            // Ordenar por comprimento, preferindo caminhos mais curtos
            usort($foundFiles, function($a, $b) {
                return strlen($a) - strlen($b);
            });
            
            // Verificar se algum dos caminhos tem 'assets' ou 'css' como componente do caminho
            $assetsMatches = array_filter($foundFiles, function($file) {
                return strpos($file, '/assets/') !== false || 
                       strpos($file, '/css/') !== false;
            });
            
            // Se encontramos caminhos com 'assets' ou 'css', usar o primeiro deles
            if (!empty($assetsMatches)) {
                $bestMatch = reset($assetsMatches);
            } else {
                // Caso contrário, usar o mais curto
                $bestMatch = $foundFiles[0];
            }
            
            $relativePath = FileUtils::makeRelativePath($sourceDir, $bestMatch);
            
            error_log("Melhor correspondência: $relativePath");
            return $relativePath;
        }
        
        // ABORDAGEM 5: Se for um arquivo incluído, tentar caminhos específicos para inclusões
        if ($isIncluded) {
            // Estas são suposições comuns para arquivos incluídos
            $commonIncludePaths = [
                './assets/' . $filename,
                '../assets/' . $filename,
                './css/' . $filename,
                '../css/' . $filename,
                './js/' . $filename,
                '../js/' . $filename,
                './includes/' . $filename,
                '../includes/' . $filename,
            ];
            
            foreach ($commonIncludePaths as $includePath) {
                error_log("Tentando caminho comum para arquivos incluídos: $includePath");
                
                // Verificar se existe a partir do diretório fonte
                $fullPath = $sourceDir . '/' . $includePath;
                if (file_exists($fullPath)) {
                    error_log("SUCESSO! Caminho de inclusão encontrado: $includePath");
                    return $includePath;
                }
            }
        }
        
        // ABORDAGEM 6: Se estamos lidando com CSS, tente procurar em diretórios de assets específicos
        if (strpos($filename, '.css') !== false) {
            $cssSpecificPaths = [
                'assets/css/' . $filename,
                'css/' . $filename,
                'styles/' . $filename,
                'assets/styles/' . $filename
            ];
            
            foreach ($cssSpecificPaths as $cssPath) {
                $fullPath = $sourceDir . '/' . $cssPath;
                error_log("Tentando caminho específico para CSS: $fullPath");
                
                if (file_exists($fullPath)) {
                    error_log("SUCESSO! Caminho CSS encontrado: $cssPath");
                    return $cssPath;
                }
                
                // Verificar na raiz do projeto também
                $rootPath = $projectRoot . '/' . $cssPath;
                if (file_exists($rootPath)) {
                    $relativePath = FileUtils::makeRelativePath($sourceDir, $rootPath);
                    error_log("SUCESSO! Caminho CSS encontrado na raiz: $relativePath");
                    return $relativePath;
                }
            }
        }
        
        // ABORDAGEM 7: Semelhante para JS
        if (strpos($filename, '.js') !== false) {
            $jsSpecificPaths = [
                'assets/js/' . $filename,
                'js/' . $filename,
                'scripts/' . $filename,
                'assets/scripts/' . $filename
            ];
            
            foreach ($jsSpecificPaths as $jsPath) {
                $fullPath = $sourceDir . '/' . $jsPath;
                error_log("Tentando caminho específico para JS: $fullPath");
                
                if (file_exists($fullPath)) {
                    error_log("SUCESSO! Caminho JS encontrado: $jsPath");
                    return $jsPath;
                }
                
                // Verificar na raiz do projeto também
                $rootPath = $projectRoot . '/' . $jsPath;
                if (file_exists($rootPath)) {
                    $relativePath = FileUtils::makeRelativePath($sourceDir, $rootPath);
                    error_log("SUCESSO! Caminho JS encontrado na raiz: $relativePath");
                    return $relativePath;
                }
            }
        }
        
        // ABORDAGEM 8: Se o caminho original tem uma parte estática após PHP com uma estrutura de diretório
        if ($hasPhpCode && strpos($staticPath, '/') !== false) {
            $staticParts = explode('/', $staticPath);
            
            // Se temos mais de um segmento (diretório/arquivo)
            if (count($staticParts) > 1) {
                $lastDir = $staticParts[count($staticParts) - 2]; // Penúltima parte deve ser o diretório
                $partialPath = $lastDir . '/' . $filename;
                
                // Verificar em vários locais comuns
                $commonParentDirs = ['', 'assets', 'css', 'js', 'images', 'includes'];
                foreach ($commonParentDirs as $parentDir) {
                    $testPath = ($parentDir ? "$parentDir/" : '') . $partialPath;
                    $fullPath = $sourceDir . '/' . $testPath;
                    
                    error_log("Tentando com estrutura parcial da variável PHP: $fullPath");
                    if (file_exists($fullPath)) {
                        error_log("SUCESSO! Estrutura parcial encontrada: $testPath");
                        return $testPath;
                    }
                    
                    // Verificar na raiz do projeto também
                    $rootPath = $projectRoot . '/' . $testPath;
                    if (file_exists($rootPath)) {
                        $relativePath = FileUtils::makeRelativePath($sourceDir, $rootPath);
                        error_log("SUCESSO! Estrutura parcial encontrada na raiz: $relativePath");
                        return $relativePath;
                    }
                }
            }
        }
        
        // ABORDAGEM 9: Para arquivos CSS/JS em particular, verificar em locais padrão comuns
        if (strpos($filename, '.css') !== false || strpos($filename, '.js') !== false) {
            $fileExt = substr($filename, strrpos($filename, '.') + 1);
            $assetDirs = [
                "assets/$fileExt",
                $fileExt,
                "assets"
            ];
            
            foreach ($assetDirs as $dir) {
                $assetPath = "$dir/$filename";
                $fullPath = $sourceDir . '/' . $assetPath;
                
                error_log("Tentando em diretório padrão para $fileExt: $fullPath");
                if (file_exists($fullPath)) {
                    error_log("SUCESSO! Encontrado em diretório padrão: $assetPath");
                    return $assetPath;
                }
                
                // Verificar na raiz do projeto
                $rootPath = $projectRoot . '/' . $assetPath;
                if (file_exists($rootPath)) {
                    $relativePath = FileUtils::makeRelativePath($sourceDir, $rootPath);
                    error_log("SUCESSO! Encontrado em diretório padrão na raiz: $relativePath");
                    return $relativePath;
                }
            }
        }
        
        // ABORDAGEM 10: Último recurso - manter apenas o nome do arquivo
        error_log("Último recurso: apenas o nome do arquivo: $filename");
        return $filename;
    }
} 