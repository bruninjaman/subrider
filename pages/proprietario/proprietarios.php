<?php
require_once __DIR__ . '/../../repositories/ProprietarioRepository.php';

$proprietarioRepo = new ProprietarioRepository();
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$resultado = $proprietarioRepo->listar($pagina);
$proprietarios = $resultado['proprietarios'];
$total = $resultado['total'];
$porPagina = 10;
$totalPaginas = ceil($total / $porPagina);
?>

<section id="proprietarios" class="spotlight style1">
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>Proprietários</h2>
                    <div class="table-wrapper">
                        <div class="table-controls mb-4">
                            <button class="button primary" onclick="window.location.href='addproprietario.php'">
                                Adicionar Proprietário
                            </button>
                            <div class="search-box">
                                <input type="text" id="busca" placeholder="Buscar proprietário..." 
                                       onkeyup="buscarProprietarios(this.value)">
                            </div>
                        </div>

                        <table class="alt">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>Telefone</th>
                                    <th>Email</th>
                                    <th>Cidade/UF</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tabela-proprietarios">
                                <?php if (empty($proprietarios)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum proprietário cadastrado.</td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($proprietarios as $prop): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($prop['nome']); ?></td>
                                        <td><?php echo htmlspecialchars(formatarCPF($prop['cpf'])); ?></td>
                                        <td><?php echo htmlspecialchars(formatarTelefone($prop['telefone'])); ?></td>
                                        <td><?php echo htmlspecialchars($prop['email']); ?></td>
                                        <td><?php echo htmlspecialchars($prop['cidade'] . '/' . $prop['estado']); ?></td>
                                        <td>
                                            <div class="button-group">
                                                <a href="editproprietario.php?id=<?php echo $prop['id']; ?>" 
                                                   class="button small">Editar</a>
                                                <button class="button small" 
                                                        onclick="confirmarExclusao(<?php echo $prop['id']; ?>)">
                                                    Excluir
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <?php if ($totalPaginas > 1): ?>
                        <div class="pagination">
                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <a href="?pagina=<?php echo $i; ?>" 
                                   class="button <?php echo $i === $pagina ? 'primary' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function formatarCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
}

function formatarTelefone(telefone) {
    telefone = telefone.replace(/\D/g, '');
    return telefone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
}

function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja excluir este proprietário?')) {
        fetch('ajax/excluir_proprietario.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Erro ao excluir proprietário: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao excluir proprietário');
        });
    }
}

function buscarProprietarios(termo) {
    if (termo.length < 3) return;
    
    fetch('ajax/buscar_proprietarios.php?termo=' + encodeURIComponent(termo))
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tabela-proprietarios');
            tbody.innerHTML = '';
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">Nenhum proprietário encontrado.</td></tr>';
                return;
            }
            
            data.forEach(prop => {
                tbody.innerHTML += `
                    <tr>
                        <td>${prop.nome}</td>
                        <td>${formatarCPF(prop.cpf)}</td>
                        <td>${formatarTelefone(prop.telefone)}</td>
                        <td>${prop.email}</td>
                        <td>${prop.cidade}/${prop.estado}</td>
                        <td>
                            <div class="button-group">
                                <a href="editproprietario.php?id=${prop.id}" class="button small">Editar</a>
                                <button class="button small" onclick="confirmarExclusao(${prop.id})">Excluir</button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao buscar proprietários');
        });
}
</script>

<style>
.table-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1em;
}

.search-box {
    flex: 0 0 300px;
}

.search-box input {
    width: 100%;
    padding: 0.5em;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.button-group {
    display: flex;
    gap: 0.5em;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5em;
    margin-top: 2em;
}

.pagination .button {
    padding: 0.5em 1em;
}
</style>

<?php
function formatarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
}

function formatarTelefone($telefone) {
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
}
?>