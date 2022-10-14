<?php
    //Criar código
   $mysqli_query        = "SELECT * FROM ordem_servicos";
   $result              = mysqli_query($conn, $mysqli_query);
   $resultados          = mysqli_num_rows($result);

   if ($resultados > 0) {
      $last_code = 0;
      while ($item = mysqli_fetch_assoc($result)) {
         $codigo = explode("/", $item["Codigo"]);
         if ($codigo[0] > $last_code && $codigo[1] == date("Y"))
            $last_code = $codigo[0];
      }
      if ($last_code < 101)
         $last_code = 100;
      $novo_codigo = ($last_code + 1) . "/" . date("Y");
   } else {
      $novo_codigo = 100 . "/" . date("Y");
   }


    $mysqli_query = "INSERT INTO ordem_servicos (motoID, Codigo, Aberto) VALUES ({$motoID},'{$novo_codigo}',1)";
    mysqli_query($conn, $mysqli_query);
    header("location: ordem.php?motoID=".$_GET["motoID"]."&ordem=".$novo_codigo);
?>