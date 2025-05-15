<!-- Include Quill Styles and Scripts -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>

<!-- Incluir bibliotecas para geração de PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

<!-- Adicionar uma meta tag para suprimir avisos de depreciação -->
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

<!-- Incluir arquivos JavaScript e CSS -->
<script src="pages/relatorio/relatorio.js" type="module"></script>
<link rel="stylesheet" href="pages/relatorio/relatorio.css">

<?php
// Obter ID da ordem de serviço
$ordem_id = isset($_GET['ordem']) ? $_GET['ordem'] : '';

// Obter informações da ordem
$query_ordem = "SELECT * FROM ordem_servicos WHERE Codigo = '$ordem_id'";
$result_ordem = mysqli_query($conn, $query_ordem);
$ordem = mysqli_fetch_assoc($result_ordem);

// Verificar se ordem existe e obter informações da motocicleta
$moto = [];

if ($ordem && isset($ordem['motoID'])) {
    $motoID = $ordem['motoID'];
    
    // Obter informações da motocicleta apenas se motoID for válido
    if ($motoID > 0) {
        $query_moto = "SELECT * FROM motocicletas WHERE motoId = " . $motoID;
        $result_moto = mysqli_query($conn, $query_moto);
        $moto = mysqli_fetch_assoc($result_moto);
    }
}
?>

<section id="banner" class="relatorio-page">
  <div class="content">
    <div class="relatorio-container">
      <form id="relatorio-form" class="form-relatorio">
        <!-- Cabeçalho do Relatório -->
        <div class="form-header">
          <img class="logo-relatorio" src="./assets/css/images/logo-branco-crop.png">
          <h1>Relatório de Ordem de Serviço</h1>
        </div>
        
        <!-- Seção de Informações Básicas -->
        <div class="form-section info-section">
          <h2>Informações da Ordem</h2>
          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="numero_ordem">Nº da Ordem:</label>
              <input type="text" id="numero_ordem" class="form-control" value="<?php echo isset($ordem['Codigo']) ? $ordem['Codigo'] : 'N/A'; ?>" readonly>
            </div>
            <div class="form-group col-md-4">
              <label for="data_ordem">Data:</label>
              <input type="text" id="data_ordem" class="form-control" value="<?php echo isset($ordem['Data']) ? date('d/m/Y', strtotime($ordem['Data'])) : 'N/A'; ?>" readonly>
            </div>
            <div class="form-group col-md-4">
              <label for="km_ordem">Quilometragem:</label>
              <input type="text" id="km_ordem" class="form-control" value="<?php echo isset($ordem['KM']) ? $ordem['KM'] : 'N/A'; ?>" readonly>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="cliente_ordem">Cliente:</label>
              <textarea id="cliente_ordem" class="form-control" readonly rows="3"><?php echo isset($moto['proprietario']) ? $moto['proprietario'] : (isset($ordem['proprietario_ordem']) ? $ordem['proprietario_ordem'] : 'N/A'); ?></textarea>
            </div>
            <div class="form-group col-md-6">
              <label for="moto_ordem">Motocicleta:</label>
              <textarea id="moto_ordem" class="form-control" readonly rows="3"><?php echo (isset($moto['marca']) && isset($moto['modelo']) && isset($moto['placa'])) ? $moto['marca'] . ' ' . $moto['modelo'] . ' (' . (isset($moto['ano']) ? $moto['ano'] : 'N/A') . ') - ' . $moto['placa'] : 'N/A'; ?></textarea>
            </div>
          </div>
          
          <?php if (isset($moto['endereco']) && !empty($moto['endereco'])): ?>
          <div class="form-row">
            <div class="form-group endereco-full-width">
              <label for="endereco_cliente">Endereço:</label>
              <textarea id="endereco_cliente" class="form-control" readonly rows="1"><?php echo $moto['endereco']; ?></textarea>
            </div>
          </div>
          <?php endif; ?>
        </div>
        
        <!-- Seção do Editor de Relatório -->
        <div class="form-section editor-section">
          <h2>Detalhes do Serviço</h2>
          <div class="form-group">
            <div id="editor-personalizado" class="editor-personalizado" contenteditable="true">
              <h3>Descrição do Serviço</h3>
              <p>Serviço realizado na motocicleta <?php echo (isset($moto['marca']) && isset($moto['modelo'])) ? $moto['marca'] . ' ' . $moto['modelo'] . ' (' . (isset($moto['ano']) ? $moto['ano'] : 'N/A') . ')' : 'N/A'; ?>, placa <?php echo isset($moto['placa']) ? $moto['placa'] : 'N/A'; ?> com <?php echo isset($ordem['KM']) ? number_format($ordem['KM'], 0, ',', '.') . ' km' : 'quilometragem não informada'; ?>.</p>
              
              <p>Diagnóstico inicial e procedimentos realizados na motocicleta:</p>
              <ul>
                <li>Verificação geral do estado da motocicleta</li>
                <li>Análise dos componentes mecânicos</li>
                <li>Testes de funcionamento</li>
              </ul>
              
              <p>Recomendações para manutenção futura:</p>
              <ul>
                <li>Próxima revisão em: <?php echo isset($ordem['KM']) ? number_format($ordem['KM'] + 5000, 0, ',', '.') . ' km' : 'quilometragem não informada'; ?></li>
                <li>Verificar níveis de óleo a cada 1.000 km</li>
                <li>Outras recomendações específicas...</li>
              </ul>
            </div>
          </div>
          
          <!-- Barra de ferramentas personalizada -->
          <div class="editor-toolbar">
            <button type="button" data-command="bold" title="Negrito"><strong>N</strong></button>
            <button type="button" data-command="underline" title="Sublinhado"><u>S</u></button>
            <button type="button" data-command="insertUnorderedList" title="Lista com marcadores"><i>•</i></button>
            <button type="button" data-command="insertOrderedList" title="Lista numerada"><i>1.</i></button>
          </div>
        </div>
        
        <!-- Seção de Finalização -->
        <div class="form-section signature-section">
          <h2>Finalização</h2>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="data-conclusao">Data de Conclusão:</label>
              <input type="date" id="data-conclusao" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group col-md-12">
              <label>Observações Finais:</label>
              <textarea id="observacoes_finais" class="form-control" rows="3" style="width: 40em;" placeholder="Observações finais sobre o serviço realizado..."></textarea>
            </div>
          </div>
        </div>
        
        <!-- Barra de Ações -->
        <div class="form-actions">
          <button type="button" id="btn-salvar" class="button primary">Salvar Relatório</button>
          <button type="button" id="btn-gerar-pdf" class="button">Download do PDF</button>
          <div class="status-message"></div>
        </div>
      </form>
    </div>
  </div>
</section>

<!-- Aguarde loader -->
<div id="aguarde" style="display: none;">
  <div class="loader"></div>
  <p>Processando, aguarde...</p>
</div>

<!-- Modal de confirmação -->
<div id="modal-confirmacao" class="modal">
  <div class="modal-content">
    <h3>Relatório salvo com sucesso!</h3>
    <p class="modal-message"></p>
    <div class="modal-buttons">
      <button id="modal-fechar" class="button primary">Fechar</button>
    </div>
  </div>
</div>

<!-- Campo oculto para armazenar o ID da ordem -->
<input type="hidden" id="ordem_id" value="<?php echo $ordem_id; ?>">