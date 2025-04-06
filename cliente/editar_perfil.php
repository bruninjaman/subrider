<?php
require_once '../includes/session_manager.php';
require_once '../includes/database.php';
require_once '../includes/validation.php';

$session = new SessionManager();

// Verifica se o cliente está logado
if (!$session->isClienteLoggedIn()) {
    header('Location: login.php');
    exit;
}

$clienteId = $session->getClienteId();
$db = new Database();
$errors = [];
$success = false;

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $celular = trim($_POST['celular'] ?? '');
    $logradouro = trim($_POST['logradouro'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $complemento = trim($_POST['complemento'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    $cep = trim($_POST['cep'] ?? '');
    
    // Validações
    if (empty($nome)) {
        $errors[] = 'O nome é obrigatório.';
    }
    
    if (empty($email)) {
        $errors[] = 'O email é obrigatório.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email inválido.';
    } else {
        // Verifica se o email já está em uso por outro cliente
        $sql = "SELECT id FROM proprietarios WHERE email = ? AND id != ? AND deleted_at IS NULL";
        $existente = $db->query($sql, [$email, $clienteId]);
        if (!empty($existente)) {
            $errors[] = 'Este email já está em uso.';
        }
    }
    
    if (empty($celular)) {
        $errors[] = 'O celular é obrigatório.';
    }
    
    if (empty($logradouro)) {
        $errors[] = 'O logradouro é obrigatório.';
    }
    
    if (empty($numero)) {
        $errors[] = 'O número é obrigatório.';
    }
    
    if (empty($bairro)) {
        $errors[] = 'O bairro é obrigatório.';
    }
    
    if (empty($cidade)) {
        $errors[] = 'A cidade é obrigatória.';
    }
    
    if (empty($estado)) {
        $errors[] = 'O estado é obrigatório.';
    }
    
    if (empty($cep)) {
        $errors[] = 'O CEP é obrigatório.';
    }
    
    // Se não houver erros, atualiza os dados
    if (empty($errors)) {
        $sql = "UPDATE proprietarios SET 
                    nome = ?,
                    email = ?,
                    telefone = ?,
                    celular = ?,
                    logradouro = ?,
                    numero = ?,
                    complemento = ?,
                    bairro = ?,
                    cidade = ?,
                    estado = ?,
                    cep = ?,
                    updated_at = NOW()
                WHERE id = ?";
                
        $params = [
            $nome,
            $email,
            $telefone,
            $celular,
            $logradouro,
            $numero,
            $complemento,
            $bairro,
            $cidade,
            $estado,
            $cep,
            $clienteId
        ];
        
        try {
            $db->execute($sql, $params);
            $success = true;
            
            // Atualiza a sessão
            $_SESSION['cliente_nome'] = $nome;
            $_SESSION['cliente_email'] = $email;
        } catch (Exception $e) {
            $errors[] = 'Erro ao atualizar os dados. Tente novamente.';
        }
    }
}

// Busca os dados atuais do cliente
$sql = "SELECT * FROM proprietarios WHERE id = ?";
$cliente = $db->query($sql, [$clienteId])[0];

$pageTitle = 'Editar Perfil - Área do Cliente';
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-edit"></i> Editar Perfil
                        </h5>
                        <a href="perfil.php" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Perfil atualizado com sucesso!
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> 
                            Por favor, corrija os seguintes erros:
                            <ul class="mb-0 mt-2">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" class="needs-validation" novalidate>
                        <div class="row">
                            <!-- Dados Pessoais -->
                            <div class="col-12 mb-4">
                                <h6>Dados Pessoais</h6>
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nome" class="form-label">Nome Completo *</label>
                                        <input type="text" class="form-control" id="nome" name="nome" 
                                               value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="telefone" class="form-label">Telefone</label>
                                        <input type="tel" class="form-control" id="telefone" name="telefone" 
                                               value="<?php echo htmlspecialchars($cliente['telefone']); ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="celular" class="form-label">Celular *</label>
                                        <input type="tel" class="form-control" id="celular" name="celular" 
                                               value="<?php echo htmlspecialchars($cliente['celular']); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Endereço -->
                            <div class="col-12">
                                <h6>Endereço</h6>
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="cep" class="form-label">CEP *</label>
                                        <input type="text" class="form-control" id="cep" name="cep" 
                                               value="<?php echo htmlspecialchars($cliente['cep']); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-8 mb-3">
                                        <label for="logradouro" class="form-label">Logradouro *</label>
                                        <input type="text" class="form-control" id="logradouro" name="logradouro" 
                                               value="<?php echo htmlspecialchars($cliente['logradouro']); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="numero" class="form-label">Número *</label>
                                        <input type="text" class="form-control" id="numero" name="numero" 
                                               value="<?php echo htmlspecialchars($cliente['numero']); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-8 mb-3">
                                        <label for="complemento" class="form-label">Complemento</label>
                                        <input type="text" class="form-control" id="complemento" name="complemento" 
                                               value="<?php echo htmlspecialchars($cliente['complemento']); ?>">
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="bairro" class="form-label">Bairro *</label>
                                        <input type="text" class="form-control" id="bairro" name="bairro" 
                                               value="<?php echo htmlspecialchars($cliente['bairro']); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="cidade" class="form-label">Cidade *</label>
                                        <input type="text" class="form-control" id="cidade" name="cidade" 
                                               value="<?php echo htmlspecialchars($cliente['cidade']); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-2 mb-3">
                                        <label for="estado" class="form-label">Estado *</label>
                                        <select class="form-select" id="estado" name="estado" required>
                                            <option value="">UF</option>
                                            <?php
                                            $estados = [
                                                'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 
                                                'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 
                                                'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
                                            ];
                                            foreach ($estados as $uf) {
                                                $selected = $uf === $cliente['estado'] ? 'selected' : '';
                                                echo "<option value=\"$uf\" $selected>$uf</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="perfil.php" class="btn btn-outline-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validação do formulário
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()

// Máscara para CEP
document.getElementById('cep').addEventListener('input', function (e) {
    var x = e.target.value.replace(/\D/g, '').match(/(\d{0,5})(\d{0,3})/);
    e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2];
});

// Máscara para telefone
document.getElementById('telefone').addEventListener('input', function (e) {
    var x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,4})(\d{0,4})/);
    e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
});

// Máscara para celular
document.getElementById('celular').addEventListener('input', function (e) {
    var x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
    e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
});

// Busca CEP
document.getElementById('cep').addEventListener('blur', function() {
    const cep = this.value.replace(/\D/g, '');
    if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('logradouro').value = data.logradouro;
                    document.getElementById('bairro').value = data.bairro;
                    document.getElementById('cidade').value = data.localidade;
                    document.getElementById('estado').value = data.uf;
                }
            });
    }
});
</script>

<?php require_once '../includes/footer.php'; ?> 