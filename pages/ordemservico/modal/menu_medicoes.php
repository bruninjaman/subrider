<link rel="stylesheet" href="<?php echo $baseAddress; ?>/pages/ordemservico/modal-menus.css">
<!-- Botões -->
<div class="buttons-table">
    <a class='button secondary' href="ordem_add_item.php?ordem=<?php echo $_GET['ordem'] ?>">Adicionar item</a>
    <!-- <a class='button secondary' href="javascript:void(0)" onclick="showModal()">Medições</a> -->

    <a id="openModal" class="button secondary">Medições</a>

    <!-- Modal Structure -->
    <div id="modal" class="modal">
        <div class="modal-content content form">
            <!-- Modal Pages -->
            <div class="modal-page" id="page1">
                <?php include("menu_escolha.php"); ?>
            </div>
            <div class="modal-page" id="cabecote" style="display:none;">
                <?php include("menu_cabecote.php"); ?>
            </div>
            <div class="modal-page" id="motorPage" style="display:none;">
                <?php include("menu_motor.php"); ?>
            </div>
            <div class="modal-page" id="virabrequimPage" style="display:none;">
                <?php include("menu_virabrequim.php"); ?>
            </div>
            <div class="modal-page" id="embreagemPage" style="display:none;">
                <?php include("menu_embreagem.php"); ?>
            </div>
            <div class="modal-page" id="bombasPage" style="display:none;">
                <?php include("menu_bomba.php"); ?>
            </div>
            <div class="modal-page" id="dados" style="display:none;">
                <?php include("dados/dados.php"); ?>
            </div>
        </div>
    </div>

    <style>
        @font-face {
            font-family: 'Inter-SemiBold';
            src: url(fonts/Inter-SemiBold.ttf);
        }

        @font-face {
            font-family: 'Inter-Bold';
            src: url(fonts/Inter-Bold.ttf);
        }

        @font-face {
            font-family: 'Inter-Medium';
            src: url(fonts/Inter-Medium.ttf);
        }

        label {
            color: gray;
            display: block;
            font-family: 'Inter-Medium';
        }

        h1 {
            font-family: 'Inter-SemiBold';
            color: white;
            text-align: center;
            background-color: #181921;
            padding: 10px;
            border-radius: 5px;
        }

        input {
            background: #17171e;
            color: white;
            height: 3em;
            border: 3px solid #2c2d34;
            border-radius: 5px;
            padding: 5px;
            font-family: 'Inter-Medium';
        }

        input[type="number"] {
            width: 120px;
        }

        input[type="number"]:focus {
            border: 3px solid #fb4545;
        }

        input[type="submit"] {
            font-family: 'Inter-SemiBold';
            background-color: #00c063;
            border-color: #00c063;
            font-size: 1.2em;
            width: 250px;
            color: white;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #e44c65;
            border-color: #e44c65;
        }

        input:focus {
            border-color: #fb4545;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="file"] {
            background: transparent;
            border: none;
        }
    </style>
    <!-- modal -->
    <div id="medicoes1" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <iframe src="medicoes.php?ordem=<?php echo $_GET['ordem'] ?>" width="100%" height="500px"></iframe>
        </div>
    </div>

    <a class='button secondary' href="relatorio.php?ordem=<?php echo $_GET['ordem'] ?>">Relatorio</a>
    <a class='button primary' href="#" onclick="gerarPDF(event)">Baixar como PDF</a>
</div>

<!-- Modal de aviso Gerando PDF -->
<div id="pdfLoadingModal" class="modal" style="display:none; background: rgba(0,0,0,0.7);">
    <div class="modal-content" style="text-align:center; padding: 40px; font-size: 1.5em; display: flex; flex-direction: column; align-items: center;">
        <div class="spinner" style="margin-bottom: 20px;"></div>
        <span>Gerando PDF<span class="dot-animate"></span></span>
    </div>
</div>
<style>
.spinner {
    border: 6px solid #f3f3f3;
    border-top: 6px solid #00c063;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.dot-animate {
    display: inline-block;
    width: 1.5em;
    text-align: left;
}
.dot-animate::after {
    content: '';
    animation: dots 1.2s steps(3, end) infinite;
}
@keyframes dots {
    0%, 20% { content: ''; }
    40% { content: '.'; }
    60% { content: '..'; }
    80%, 100% { content: '...'; }
}
</style>
<script>
function gerarPDF(event) {
    event.preventDefault();
    var modal = document.getElementById('pdfLoadingModal');
    modal.style.display = 'block';
    var ordem = new URLSearchParams(window.location.search).get('ordem');
    var url = 'pdf/download.php?ordem=' + ordem;
    fetch(url)
        .then(response => response.blob())
        .then(blob => {
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'ordem_' + ordem + '.pdf';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            setTimeout(function() {
                modal.style.display = 'none';
            }, 1000); // Esconde o modal logo após iniciar o download
        })
        .catch(() => {
            modal.style.display = 'none';
            alert('Erro ao gerar o PDF.');
        });
}
</script>
