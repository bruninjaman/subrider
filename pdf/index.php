<?php
    //AUTOLOAD COMPOSER
    require __DIR__ . "/vendor/autoload.php";

    use Dompdf\Dompdf;
    use Dompdf\Options;

    $options = new Options();
    $options->setChroot(__DIR__);
    $options->setIsRemoteEnabled(true);

    $dompdf = new Dompdf($options);
    
    //$dompdf->loadHtml('Teste');
    $dompdf->loadHtmlFile(__DIR__ . '/ordem.html');

    $dompdf->render();

    header('content-type: application/pdf');
    echo $dompdf->output();
?>