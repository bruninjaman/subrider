<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <form id="formAddMoto" method="post" action="scripts/tabelaMotos/add-moto.php" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12">
                    <h2>Adicionar novo veículo</h2>
                    <?php if (isset($_SESSION['msg'])): ?>
                        <div class="alert <?php echo $_SESSION['msg_type']; ?>">
                            <?php 
                            echo $_SESSION['msg']; 
                            unset($_SESSION['msg']);
                            unset($_SESSION['msg_type']);
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="upload-image">
                        <div class="card thmb">
                            <img id="preview" src="assets/images/placeholder-moto.png" alt="preview" />
                            <input type="file" name="foto" accept="image/*" onchange="previewImage(this);" />
                            <i class="fas fa-arrow-circle-up"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label>Endereço:</label>
                    <input type="text" name="endereco" id="endereco" required>
                    <small class="form-text text-muted">Informe o endereço completo</small>
                </div>
            </div>
            <div class="row">
                <div class="col-3">
                    <label>Ano:</label>
                    <input type="text" name="ano" id="ano" minlength="4" maxlength="4" required>
                    <small class="form-text text-muted">Ex: 2024</small>
                </div>
                <div class="col-5">
                    <label>Modelo:</label>
                    <input type="text" name="modelo" id="modelo" required>
                    <small class="form-text text-muted">Ex: CG 160</small>
                </div>
                <div class="col-4">
                    <label>Marca:</label>
                    <input type="text" name="marca" id="marca" required>
                    <small class="form-text text-muted">Ex: Honda</small>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <label>Proprietário:</label>
                    <input type="text" name="proprietario" id="proprietario" required>
                    <small class="form-text text-muted">Digite para buscar proprietários cadastrados</small>
                </div>
                <div class="col-4">
                    <label>Placa:</label>
                    <input type="text" name="placa" id="placa" required>
                    <small class="form-text text-muted">Ex: ABC-1234</small>
                </div>
                <div class="col-2">
                    <label>KM:</label>
                    <input type="text" name="KM" id="KM" required>
                    <small class="form-text text-muted">Ex: 1.234</small>
                </div>
            </div>
            <hr>
            <button type="submit" id="btnSalvar" class="button primary">
                <i class="fas fa-save"></i> Salvar
            </button>
            <a href="tabelaMotos.php" class="button">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </form>
    </div>
</section>

<script src="assets/js/global/jquery.mask.min.js"></script>
<script src="assets/js/global/jquery.validate.min.js"></script>
<script src="assets/js/global/messages_pt_BR.min.js"></script>
<script src="assets/js/global/additional-methods.min.js"></script>
<script src="assets/js/global/jquery-ui.min.js"></script>

<script>
// Preview da imagem
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#preview').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$(document).ready(function() {
    // Máscara para placa (formato Mercosul)
    $('#placa').mask('SSS-0A00', {
        translation: {
            'S': {pattern: /[A-Za-z]/},
            'A': {pattern: /[A-Za-z0-9]/},
            '0': {pattern: /[0-9]/}
        }
    });

    // Máscara para ano
    $('#ano').mask('0000');

    // Máscara para quilometragem
    $('#KM').mask('000.000', {reverse: true});

    // Autocomplete para proprietários
    $('#proprietario').autocomplete({
        source: 'ajax/buscar_proprietarios.php',
        minLength: 2
    });

    // Validação do formulário
    $('#formAddMoto').validate({
        rules: {
            endereco: {
                required: true,
                minlength: 5
            },
            ano: {
                required: true,
                minlength: 4,
                maxlength: 4,
                min: 1900,
                max: new Date().getFullYear() + 1
            },
            modelo: {
                required: true,
                minlength: 2
            },
            marca: {
                required: true,
                minlength: 2
            },
            proprietario: {
                required: true
            },
            placa: {
                required: true,
                minlength: 7
            },
            KM: {
                required: true,
                min: 0
            }
        },
        messages: {
            endereco: {
                required: "Por favor, informe o endereço",
                minlength: "O endereço deve ter pelo menos 5 caracteres"
            },
            ano: {
                required: "Por favor, informe o ano",
                minlength: "O ano deve ter 4 dígitos",
                maxlength: "O ano deve ter 4 dígitos",
                min: "Ano inválido",
                max: "Ano inválido"
            },
            modelo: {
                required: "Por favor, informe o modelo",
                minlength: "O modelo deve ter pelo menos 2 caracteres"
            },
            marca: {
                required: "Por favor, informe a marca",
                minlength: "A marca deve ter pelo menos 2 caracteres"
            },
            proprietario: {
                required: "Por favor, informe o proprietário"
            },
            placa: {
                required: "Por favor, informe a placa",
                minlength: "A placa deve ter 7 caracteres"
            },
            KM: {
                required: "Por favor, informe a quilometragem",
                min: "A quilometragem deve ser maior que 0"
            }
        },
        submitHandler: function(form) {
            // Adiciona loading
            $('#btnSalvar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvando...');
            
            // Submit do formulário
            form.submit();
        }
    });
});
</script>