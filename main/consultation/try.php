<?php
require '../../vendor/autoload.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();
$html = require_once("invoice.php");
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('invoice.pdf');
?>
