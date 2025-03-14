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
    <a class='button primary' href="pdf/download.php?ordem=<?php echo $_GET['ordem'] ?>">Baixar como PDF</a>
</div>