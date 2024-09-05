<style>
</style>
<section id="two">
    <center>
        <div class="content">
            <?php
            $specsinfo_query = "SELECT * FROM specs ";
            $specsinfo_query .= " WHERE (SELECT ordem_servicos.motoID FROM ordem_servicos WHERE ordem_servicos.Codigo = '" . $_GET['ordem'] . "') = motoID";

            $specsinfo_result = mysqli_query($conn, $specsinfo_query);

            while ($specsinfo = mysqli_fetch_assoc($specsinfo_result)) {
                if (mysqli_num_rows($specsinfo_result) > 0) {
            ?>
                    <div class="row">
                        <div class="col-6">
                            <ul>
                                <li><b>Descrição: </b> <?php echo $specsinfo['spec_desc'] ?></li>
                                <li><b>Valor Encontrado: </b> <?php echo $specsinfo['valor'] ?></li>
                            </ul>
                        </div>
                    </div>
                <?php
                }
                ?>
            <?php
            }
            ?>
        </div>
    </center>
</section>