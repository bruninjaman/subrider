<?php
echo "<style>";
echo file_get_contents($baseAddress . '/pages/ordemservico/modal/modal.css');
echo "</style>";
?>

<style>
    #editableproprietario {
        font-size: 25px;
    }

    .headers-tabela {
        background-color: #181921;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 15px;
    }

    .category-header {
        background-color: #2c2d34;
        color: white;
        padding: 8px;
        border-radius: 5px;
        margin: 15px 0 5px 0;
        font-weight: bold;
    }
    
    .item-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 10px;
    }
    
    .item-description {
        display: flex;
        align-items: center;
    }
    
    .item-details {
        flex: 1;
    }
    
    .table-section {
        margin-bottom: 20px;
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 5px;
        padding: 15px;
    }
    
    .table-section-title {
        font-size: 1.2em;
        margin-bottom: 10px;
        color: #e44c65;
    }
    
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 5px;
    }
    
    .action-button {
        background: none;
        border: none;
        padding: 5px;
        cursor: pointer;
        transition: transform 0.2s ease;
        border-radius: 4px;
    }
    
    .action-button:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: scale(1.1);
    }
    
    .action-button img {
        height: 24px;
        width: 24px;
    }
    
    .table td, .table th {
        padding: 8px 10px;
        text-align: left;
        vertical-align: middle;
    }
    
    .total td {
        font-weight: bold;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .totalbold {
        font-size: 1.1em;
        color: #e44c65;
    }
    
    /* Responsividade para dispositivos móveis */
    @media screen and (max-width: 768px) {
        .table th, .table td {
            padding: 6px 4px;
            font-size: 0.9em;
        }
        
        .action-button img {
            height: 20px;
            width: 20px;
        }
        
        .item-image {
            width: 40px;
            height: 40px;
        }
    }
</style>

<?php
// Incluir o script de busca de imagens
include_once(__DIR__ . '/../../scripts/peca_imagem.php');

$get_ordemservicos = "SELECT * FROM ordem_servicos ";
$get_ordemservicos .= "WHERE ordem_servicos.Codigo = '" . $_GET['ordem'] . "' ";
$ordem_servicos = mysqli_query($conn, $get_ordemservicos);
$ordem_servicos = mysqli_fetch_assoc($ordem_servicos);
?>
<section id="banner">
    <div class="content">
        <!-- definir proprietário -->
        <h2 class="headers-tabela">Ordem de Serviço: <?php echo $_GET['ordem'] ?></h2>

        <h1 class="headers-tabela" id="editableData">Data: <span id="dateValue"><?php echo ($ordem_servicos["Data"] != null) ? date("d/m/Y", strtotime($ordem_servicos["Data"])) : "dd/mm/aaaa"; ?></span></h1>

        <?php
        if ($ordem_servicos["proprietario_ordem"] == null) {
            // pegar o proprietario
            $get_proprietario_query = "SELECT proprietario ";
            $get_proprietario_query .= "FROM motocicletas ";
            $get_proprietario_query .= "WHERE (SELECT ordem_servicos.motoID FROM ordem_servicos WHERE ordem_servicos.Codigo  = '" . $_GET['ordem'] . "') = motocicletas.motoId";
            $proprietario = mysqli_query($conn, $get_proprietario_query);
            $proprietario = mysqli_fetch_assoc($proprietario);

            // modificar proprietario da ordem
            $atualizar_proprietario = "UPDATE ordem_servicos ";
            $atualizar_proprietario .= "SET ordem_servicos.proprietario_ordem = '" . $proprietario['proprietario'] . "' ";
            $atualizar_proprietario .= "WHERE ordem_servicos.Codigo = '" . $_GET['ordem'] . "' ";
            mysqli_query($conn, $atualizar_proprietario);


            // atualizar array ordem servicos
            $ordem_servicos = mysqli_query($conn, $get_ordemservicos);
            $ordem_servicos = mysqli_fetch_assoc($ordem_servicos);
        }
        ?>
        <h1 class="headers-tabela">Proprietário: <span id="editableproprietario"><?php echo ($ordem_servicos["proprietario_ordem"] != null) ? $ordem_servicos["proprietario_ordem"] : "Não definido"; ?></span></h1>
        <?php
        $query = "SELECT KM FROM `ordem_servicos` WHERE '" . $_GET['ordem'] . "' = `ordem_servicos`.`Codigo`;";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $quilometragem = $row['KM'];
        } else {
            $quilometragem = "Não encontrado";
        }
        if ($quilometragem == null) {
            $quilometragem = "Não encontrado";
        }
        ?>
        <h1 class="headers-tabela">Quilometragem: <span><?php echo $quilometragem; ?></span></h1>

        <p id="errorMessage" style="color: red; display: none;"></p>

        <?php
        // Consulta todos os itens da ordem
        $items_ordem_query = "SELECT * FROM item_ordem WHERE item_ordem.Ordem = '" . $_GET['ordem'] . "' ";
        $result = mysqli_query($conn, $items_ordem_query);
        
        // Separar itens por categoria
        $pecas = [];
        $servicos = [];
        $adiantamentos = [];
        
        while ($item = mysqli_fetch_assoc($result)) {
            if ($item['Categoria'] == '3') {
                $adiantamentos[] = $item;
            } 
            // Peças (assumindo que categoria 2 é para peças)
            else if ($item['Categoria'] == '2') {
                $pecas[] = $item;
            } 
            // Serviços (assumindo que categoria 1 é para serviços)
            else {
                $servicos[] = $item;
            }
        }
        
        // Cálculos de totais
        $total_pecas = 0;
        $total_servicos = 0;
        $total_adiantamentos = 0;
        
        foreach ($pecas as $item) {
            $total_pecas += $item['Valor'] * $item['Quantidade'];
        }
        
        foreach ($servicos as $item) {
            $total_servicos += $item['Valor'] * $item['Quantidade'];
        }
        
        foreach ($adiantamentos as $item) {
            $total_adiantamentos += $item['Valor'] * $item['Quantidade'];
        }
        
        $total_geral = $total_pecas + $total_servicos;
        $saldo = $total_geral - $total_adiantamentos;
        ?>

        <div class="table-wrapper">
            <!-- Tabela de Serviços -->
            <div class="table-section">
                <div class="table-section-title">Serviços</div>
                <table class="table alt">
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Quantidade</th>
                            <th>Valor unitário</th>
                            <th>Valor Total</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($servicos) > 0) {
                            foreach ($servicos as $item) {
                        ?>
                            <tr>
                                <td data-cell="Descrição">
                                    <div class="item-description">
                                        <div class="item-details">
                                            <?php echo $item['Tipo'] != '0' ? "" . $item['Tipo'] . " - " : ""; ?>
                                            <?php echo $item['Grupo'] != '0' ? "" . $item['Grupo'] . " - " : ""; ?>
                                            <?php echo $item['Item'] != '0' ? "" . $item['Item'] . "" : ""; ?>
                                            <?php echo $item['Parte'] != '0' ? " / " . $item['Parte'] : ""; ?>
                                            <?php echo $item['Descricao'] != '0' ? "" . $item['Descricao'] : ""; ?>
                                        </div>
                                    </div>
                                </td>
                                <td data-cell="Quantidade"><?php echo $item['Quantidade']; ?></td>
                                <td data-cell="Valor Unitário"><?php echo ($item['Valor'] <= 0) ? 'N/D' : realFormat($item['Valor']); ?></td>
                                <td data-cell="Valor Total"><?php echo realFormat($item['Valor'] * $item['Quantidade']); ?></td>
                                <td data-cell="Ações" class="action-buttons">
                                    <button class="action-button edit-button" onclick="location.href='<?php echo $baseAddress; ?>/ordem_edit_item.php?item_ordemID=<?php echo $item['item_ordemID'] ?>&ordem=<?php echo $_GET['ordem'] ?>'">
                                        <img src="<?php echo $baseAddress; ?>/assets/css/images/edit.png" title="Editar">
                                    </button>
                                    <button class="action-button delete-button" onclick="return delete_confirm('Deseja realmente excluir este item?',<?php echo $item['item_ordemID'] ?>,'<?php echo $_GET['ordem'] ?>')">
                                        <img src="<?php echo $baseAddress; ?>/assets/css/images/x-button.png" title="Excluir">
                                    </button>
                                </td>
                            </tr>
                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">Nenhum serviço adicionado</td>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr class="total">
                            <td colspan="3" data-cell=""></td>
                            <td data-cell="Subtotal">Total Serviços:</td>
                            <td data-cell="Valor"><?php echo realFormat($total_servicos) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Tabela de Peças -->
            <div class="table-section">
                <div class="table-section-title">Peças</div>
                <table class="table alt">
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Quantidade</th>
                            <th>Valor unitário</th>
                            <th>Valor Total</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($pecas) > 0) {
                            foreach ($pecas as $item) {
                                // Buscar imagem da peça
                                $imagem_peca = '';
                                
                                // Primeiro, verificar se o item já tem um campo Foto
                                if ($item['Foto'] != '0' && !empty($item['Foto'])) {
                                    // Usar a foto diretamente do item da ordem
                                    $imagem_peca = $baseAddress . '/' . $item['Foto'];
                                } 
                                // Se não tem foto direta, tentar buscar pelo código
                                else if ($item['Codigo'] != '0' && !empty($item['Codigo'])) {
                                    // Usar a função que criamos para buscar a imagem
                                    $imagem_peca = buscarImagemPeca($item['Codigo']);
                                } 
                                // Se nada funcionar, tentar buscar pelo ID do item, que pode corresponder ao pecaId
                                else if ($item['Item'] != '0' && !empty($item['Item'])) {
                                    // Pode haver correspondência com a tabela pecas
                                    $imagem_peca = buscarImagemPeca($item['Item']);
                                }
                                // Se ainda não tiver imagem, usar a padrão
                                else {
                                    $imagem_peca = $baseAddress . '/assets/css/images/peca-padrao.png';
                                }
                        ?>
                            <tr>
                                <td data-cell="Descrição">
                                    <div class="item-description">
                                        <img src="<?php echo $imagem_peca; ?>" alt="Imagem da Peça" class="item-image">
                                        <div class="item-details">
                                            <?php echo $item['Tipo'] != '0' ? "" . $item['Tipo'] . " - " : ""; ?>
                                            <?php echo $item['Grupo'] != '0' ? "" . $item['Grupo'] . " - " : ""; ?>
                                            <?php echo $item['Item'] != '0' ? "" . $item['Item'] . "" : ""; ?>
                                            <?php echo $item['Parte'] != '0' ? " / " . $item['Parte'] : ""; ?>
                                            <?php echo $item['Descricao'] != '0' ? "" . $item['Descricao'] : ""; ?>
                                            <?php echo $item['Codigo'] != '0' ? " (" . $item['Codigo'] . ")" : ""; ?>
                                        </div>
                                    </div>
                                </td>
                                <td data-cell="Quantidade"><?php echo $item['Quantidade']; ?></td>
                                <td data-cell="Valor Unitário"><?php echo ($item['Valor'] <= 0) ? 'N/D' : realFormat($item['Valor']); ?></td>
                                <td data-cell="Valor Total"><?php echo realFormat($item['Valor'] * $item['Quantidade']); ?></td>
                                <td data-cell="Ações" class="action-buttons">
                                    <button class="action-button edit-button" onclick="location.href='<?php echo $baseAddress; ?>/ordem_edit_item.php?item_ordemID=<?php echo $item['item_ordemID'] ?>&ordem=<?php echo $_GET['ordem'] ?>'">
                                        <img src="<?php echo $baseAddress; ?>/assets/css/images/edit.png" title="Editar">
                                    </button>
                                    <button class="action-button delete-button" onclick="return delete_confirm('Deseja realmente excluir este item?',<?php echo $item['item_ordemID'] ?>,'<?php echo $_GET['ordem'] ?>')">
                                        <img src="<?php echo $baseAddress; ?>/assets/css/images/x-button.png" title="Excluir">
                                    </button>
                                </td>
                            </tr>
                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">Nenhuma peça adicionada</td>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr class="total">
                            <td colspan="3" data-cell=""></td>
                            <td data-cell="Subtotal">Total Peças:</td>
                            <td data-cell="Valor"><?php echo realFormat($total_pecas) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Resumo de Valores -->
            <div class="table-section">
                <div class="table-section-title">Resumo</div>
                <table class="table">
                    <tbody>
                        <tr>
                            <td><strong>Total de Serviços:</strong></td>
                            <td><?php echo realFormat($total_servicos) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Total de Peças:</strong></td>
                            <td><?php echo realFormat($total_pecas) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Valor Total:</strong></td>
                            <td><?php echo realFormat($total_geral) ?></td>
                        </tr>
                        
                        <?php if (count($adiantamentos) > 0): ?>
                        <tr>
                            <td colspan="2" class="category-header">Adiantamentos</td>
                        </tr>
                        <?php foreach ($adiantamentos as $item): ?>
                        <tr>
                            <td><?php echo $item['Descricao'] ?></td>
                            <td><?php echo realFormat($item['Valor'] * $item['Quantidade']) ?></td>
                            <td width="50">
                                <button class="action-button delete-button" onclick="return delete_confirm('Deseja realmente excluir este adiantamento?',<?php echo $item['item_ordemID'] ?>,'<?php echo $_GET['ordem'] ?>')">
                                    <img src="<?php echo $baseAddress; ?>/assets/css/images/x-button.png" title="Excluir" style="height: 20px; width: 20px;">
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td><strong>Total de Adiantamentos:</strong></td>
                            <td><?php echo realFormat($total_adiantamentos) ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <tr class="total">
                            <td class="totalbold" data-cell="Saldo"><strong>Saldo a Pagar:</strong></td>
                            <td class="totalbold" data-cell="Valor"><?php echo realFormat($saldo) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <form action="<?php echo $baseAddress; ?>/scripts/ordemservico/register_medicoes.php?ordem=<?php echo (string)$_GET['ordem'] ?>" method="POST">
                <input type="hidden" id="selected_option" name="selected_option" value="">
                <?php 
                    include('modal/menu_medicoes.php');
                ?>
            </form>
        </div>
    </div>
</section>
<?php
echo "<script>";
echo file_get_contents($baseAddress . '/pages/ordemservico/modal/modal.js');
echo "</script>";
?>