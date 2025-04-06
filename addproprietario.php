<?php
session_start();
require_once __DIR__ . '/repositories/ProprietarioRepository.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $proprietarioRepo = new ProprietarioRepository();
    $resultado = $proprietarioRepo->criar($_POST);
    
    if ($resultado) {
        $_SESSION['mensagem'] = 'Proprietário cadastrado com sucesso!';
        $_SESSION['tipo_mensagem'] = 'success';
        header('Location: proprietario.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Erro ao cadastrar proprietário.';
        $_SESSION['tipo_mensagem'] = 'error';
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Adicionar Proprietário - SubRider</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <link rel="stylesheet" href="assets/css/form.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
</head>
<body class="is-preload">
    <div id="page-wrapper">
        <?php require("./pages/proprietario/header.php"); ?>

        <section id="content" class="main">
            <div class="container">
                <h2>Adicionar Proprietário</h2>
                
                <?php if (isset($_SESSION['mensagem'])): ?>
                <div class="alert <?php echo $_SESSION['tipo_mensagem']; ?>">
                    <?php 
                    echo $_SESSION['mensagem'];
                    unset($_SESSION['mensagem']);
                    unset($_SESSION['tipo_mensagem']);
                    ?>
                </div>
                <?php endif; ?>

                <form method="post" action="addproprietario.php" onsubmit="return validarFormulario()">
                    <div class="row gtr-uniform">
                        <div class="col-6">
                            <label for="nome">Nome Completo *</label>
                            <input type="text" name="nome" id="nome" required 
                                   placeholder="Nome completo do proprietário" />
                        </div>
                        <div class="col-6">
                            <label for="cpf">CPF *</label>
                            <input type="text" name="cpf" id="cpf" required 
                                   placeholder="000.000.000-00" maxlength="14" />
                        </div>
                        <div class="col-6">
                            <label for="telefone">Telefone *</label>
                            <input type="text" name="telefone" id="telefone" required 
                                   placeholder="(00) 00000-0000" maxlength="15" />
                        </div>
                        <div class="col-6">
                            <label for="email">E-mail</label>
                            <input type="email" name="email" id="email" 
                                   placeholder="email@exemplo.com" />
                        </div>
                        <div class="col-12">
                            <label for="endereco">Endereço *</label>
                            <input type="text" name="endereco" id="endereco" required 
                                   placeholder="Rua, número, bairro" />
                        </div>
                        <div class="col-6">
                            <label for="cidade">Cidade *</label>
                            <input type="text" name="cidade" id="cidade" required 
                                   placeholder="Nome da cidade" />
                        </div>
                        <div class="col-3">
                            <label for="estado">Estado *</label>
                            <select name="estado" id="estado" required>
                                <option value="">Selecione...</option>
                                <option value="AC">AC</option>
                                <option value="AL">AL</option>
                                <option value="AP">AP</option>
                                <option value="AM">AM</option>
                                <option value="BA">BA</option>
                                <option value="CE">CE</option>
                                <option value="DF">DF</option>
                                <option value="ES">ES</option>
                                <option value="GO">GO</option>
                                <option value="MA">MA</option>
                                <option value="MT">MT</option>
                                <option value="MS">MS</option>
                                <option value="MG">MG</option>
                                <option value="PA">PA</option>
                                <option value="PB">PB</option>
                                <option value="PR">PR</option>
                                <option value="PE">PE</option>
                                <option value="PI">PI</option>
                                <option value="RJ">RJ</option>
                                <option value="RN">RN</option>
                                <option value="RS">RS</option>
                                <option value="RO">RO</option>
                                <option value="RR">RR</option>
                                <option value="SC">SC</option>
                                <option value="SP">SP</option>
                                <option value="SE">SE</option>
                                <option value="TO">TO</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="cep">CEP *</label>
                            <input type="text" name="cep" id="cep" required 
                                   placeholder="00000-000" maxlength="9" />
                        </div>
                        <div class="col-12">
                            <ul class="actions">
                                <li><input type="submit" value="Cadastrar" class="primary" /></li>
                                <li><input type="button" value="Cancelar" 
                                         onclick="window.location.href='proprietario.php'" /></li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <?php require("./pages/proprietario/footer.php"); ?>
    </div>

    <script src="assets/js/global/jquery.min.js"></script>
    <script src="assets/js/global/jquery.scrolly.min.js"></script>
    <script src="assets/js/global/jquery.dropotron.min.js"></script>
    <script src="assets/js/global/jquery.scrollex.min.js"></script>
    <script src="assets/js/global/browser.min.js"></script>
    <script src="assets/js/global/breakpoints.min.js"></script>
    <script src="assets/js/global/util.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Máscara para CPF
        const cpfInput = document.getElementById('cpf');
        cpfInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            this.value = value;
        });

        // Máscara para telefone
        const telefoneInput = document.getElementById('telefone');
        telefoneInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            if (value.length > 2) value = '(' + value.slice(0,2) + ') ' + value.slice(2);
            if (value.length > 9) value = value.slice(0,9) + '-' + value.slice(9);
            this.value = value;
        });

        // Máscara para CEP
        const cepInput = document.getElementById('cep');
        cepInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 8) value = value.slice(0, 8);
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            this.value = value;
        });

        // Busca CEP
        cepInput.addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById('endereco').value = data.logradouro;
                            document.getElementById('cidade').value = data.localidade;
                            document.getElementById('estado').value = data.uf;
                        }
                    })
                    .catch(error => console.error('Erro:', error));
            }
        });
    });

    function validarFormulario() {
        const cpf = document.getElementById('cpf').value.replace(/\D/g, '');
        if (!validarCPF(cpf)) {
            alert('CPF inválido!');
            return false;
        }
        return true;
    }

    function validarCPF(cpf) {
        if (cpf.length !== 11) return false;
        
        // Verifica se todos os dígitos são iguais
        if (/^(\d)\1+$/.test(cpf)) return false;
        
        // Validação do primeiro dígito verificador
        let soma = 0;
        for (let i = 0; i < 9; i++) {
            soma += parseInt(cpf.charAt(i)) * (10 - i);
        }
        let resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf.charAt(9))) return false;
        
        // Validação do segundo dígito verificador
        soma = 0;
        for (let i = 0; i < 10; i++) {
            soma += parseInt(cpf.charAt(i)) * (11 - i);
        }
        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf.charAt(10))) return false;
        
        return true;
    }
    </script>
</body>
</html> 