<style>
    .modal-menu {
        padding: 20px;
        background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    .modal-menu h2 {
        color: #e44c5c;
        text-align: center;
        margin-bottom: 25px;
        font-size: 1.5em;
        font-weight: 600;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .modal-menu .button-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .modal-menu .button.secondary {
        background: linear-gradient(135deg, #e44c5c 0%, #c73650 100%) !important;
        color: white !important;
        border: none !important;
        padding: 12px 20px !important;
        border-radius: 8px !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 4px 15px rgba(228, 76, 92, 0.3) !important;
        text-align: center !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    .modal-menu .button.secondary:hover {
        background: linear-gradient(135deg, #c73650 0%, #a82d44 100%) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(228, 76, 92, 0.4) !important;
        color: white !important;
    }
    
    .modal-menu .button.secondary:active {
        transform: translateY(0) !important;
        box-shadow: 0 2px 10px rgba(228, 76, 92, 0.3) !important;
    }
    
    .modal-menu #btnDados {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%) !important;
        box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3) !important;
    }
    
    .modal-menu #btnDados:hover {
        background: linear-gradient(135deg, #e67e22 0%, #d35400 100%) !important;
        box-shadow: 0 6px 20px rgba(243, 156, 18, 0.4) !important;
    }
    
    .modal-menu .button.primary.button--sair {
        background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%) !important;
        color: white !important;
        border: none !important;
        padding: 12px 25px !important;
        border-radius: 8px !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 4px 15px rgba(149, 165, 166, 0.3) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        margin: 0 auto !important;
        width: fit-content !important;
    }
    
    .modal-menu .button.primary.button--sair:hover {
        background: linear-gradient(135deg, #7f8c8d 0%, #34495e 100%) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(149, 165, 166, 0.4) !important;
    }
    
    .modal-menu .button.primary.button--sair i {
        font-size: 1.1em;
    }
</style>
<div class="modal-menu">
    <h2>Selecione uma Opção</h2>
    <div class="button-grid">
        <a class="button secondary" id="btnDados" onclick="setFormSection('dados')">Dados</a>
        <a class="button secondary" id="btnCabecote" onclick="setFormSection('cabecote')">Cabeçote</a>
        <a class="button secondary" id="btnMotor" onclick="setFormSection('motor')">Motor</a>
        <a class="button secondary" id="btnVirabrequim" onclick="setFormSection('virabrequim')">Virabrequim</a>
        <a class="button secondary" id="btnEmbreagem" onclick="setFormSection('embreagem')">Embreagem</a>
        <a class="button secondary" id="btnBombas" onclick="setFormSection('bomba')">Bombas</a>
    </div>
    <a class="button primary button--sair" id="closeModal3"><i class="fas fa-sign-out-alt"></i> Sair</a>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">