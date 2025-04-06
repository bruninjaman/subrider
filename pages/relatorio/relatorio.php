<?php
require_once __DIR__ . '/../../classes/Relatorio.php';

// Verifica se foi passado o número da ordem
if (!isset($_GET['ordem'])) {
    die('Ordem não especificada');
}

// Cria uma instância do relatório
$relatorio = new Relatorio($conn, $_GET['ordem']);

// Se for uma requisição POST, salva o relatório
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $observacoes = $_POST['observacoes'] ?? '';
    if ($relatorio->salvar($observacoes)) {
        // Redireciona para o download do PDF
        header('Location: ' . $baseAddress . '/pdf/download.php?ordem=' . $_GET['ordem']);
        exit;
    }
}

// Gera o HTML do relatório
$html = $relatorio->gerarHTML();

// Exibe o formulário para adicionar observações
?>
<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <div class="relatorio-preview">
            <?php echo $html; ?>
        </div>
        
        <form method="post" class="observacoes-form">
            <div class="row">
                <div class="col-12">
                    <label for="observacoes">Observações Adicionais</label>
                    <textarea name="observacoes" id="observacoes" rows="4" placeholder="Digite aqui observações adicionais para o relatório..."></textarea>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <ul class="actions">
                        <li>
                            <input type="submit" class="button primary" value="Gerar PDF">
                        </li>
                        <li>
                            <a href="<?php echo $baseAddress; ?>/ordemservico.php?ordem=<?php echo $_GET['ordem']; ?>" class="button">Voltar</a>
                        </li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</section>

<style>
.relatorio-preview {
    background: white;
    padding: 20px;
    margin-bottom: 30px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.observacoes-form {
    background: rgba(255,255,255,0.1);
    padding: 20px;
    border-radius: 5px;
}

.observacoes-form label {
    color: white;
    margin-bottom: 10px;
    display: block;
}

.observacoes-form textarea {
    width: 100%;
    background: rgba(255,255,255,0.9);
    border: 1px solid rgba(255,255,255,0.2);
    padding: 10px;
    color: #333;
}

.actions {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    gap: 10px;
}
</style>