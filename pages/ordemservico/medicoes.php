<style>
    .medicoes-container {
        padding: 20px;
        border-radius: 5px;
        background-color: rgba(255, 255, 255, 0.05);
        margin-bottom: 20px;
    }
    
    .medicoes-title {
        color: #e44c65;
        font-size: 1.5em;
        margin-bottom: 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 10px;
    }
    
    .medicoes-item {
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 5px;
        background-color: rgba(255, 255, 255, 0.05);
        display: flex;
        justify-content: space-between;
    }
    
    .medicoes-label {
        font-weight: bold;
    }
    
    .medicoes-value {
        color: #00c063;
    }
    
    .medicoes-empty {
        text-align: center;
        padding: 20px;
        color: #999;
        font-style: italic;
    }
    
    @media screen and (max-width: 768px) {
        .medicoes-item {
            flex-direction: column;
        }
        
        .medicoes-value {
            margin-top: 5px;
        }
    }
</style>
<section id="two">
    <div class="medicoes-container">
        <div class="medicoes-title">Medições e Especificações</div>
        
        <?php
        $specsinfo_query = "SELECT * FROM specs ";
        $specsinfo_query .= " WHERE (SELECT ordem_servicos.motoID FROM ordem_servicos WHERE ordem_servicos.Codigo = '" . $_GET['ordem'] . "') = motoID";

        $specsinfo_result = mysqli_query($conn, $specsinfo_query);
        
        if (mysqli_num_rows($specsinfo_result) > 0) {
            while ($specsinfo = mysqli_fetch_assoc($specsinfo_result)) {
        ?>
                <div class="medicoes-item">
                    <div class="medicoes-label"><?php echo $specsinfo['spec_desc'] ?></div>
                    <div class="medicoes-value"><?php echo $specsinfo['valor'] ?></div>
                </div>
        <?php
            }
        } else {
        ?>
            <div class="medicoes-empty">
                Nenhuma medição registrada para esta ordem de serviço.
            </div>
        <?php
        }
        ?>
    </div>
</section>