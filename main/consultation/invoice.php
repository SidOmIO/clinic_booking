<?php
session_start();
if(!isset($_SESSION['login']) && !isset($_SESSION['type'])) {
header("location: ../../index.php");
}
$items = $_POST['items'];

$line_items = [];
foreach ($items as $item) {
    $unit_amount = (int)($item['price'] * 100);

    $line_items[] = [
            'description' => $item['name'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['price']
    ];
}

$invoiceData = [
    'invoice_number' => 'INV-'.$_POST['invNo'],
    'date' => $_POST['date'],
    'bill_to' => [
        'name' => $_POST['name'],
        'email' => $_POST['email']
    ],
    'items' => $line_items,
    'notes' => 'Thank you for your business!'
];

function calculateTotals($items) {
    $total = 0;
    foreach ($items as $item) {
        $total += $item['quantity'] * $item['unit_price'];
    }

    return ['total' => $total];
}

$totals = calculateTotals($invoiceData['items']);
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .invoice-box { width: calc(100% - 2cm); margin: 1cm auto; padding: 1cm; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        .invoice-box table tr td:nth-child(2) { text-align: right; }
        .invoice-box table tr.top table td { padding-bottom: 20px; }
        .invoice-box table tr.information table td { padding-bottom: 40px; }
        .invoice-box table tr.heading td { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; }
        .invoice-box table tr.details td { padding-bottom: 20px; }
        .invoice-box table tr.item td { border-bottom: 1px solid #eee; }
        .invoice-box table tr.item.last td { border-bottom: none; }
        .invoice-box table tr.total td:nth-child(2) { border-top: 2px solid #eee; font-weight: bold; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <h1>Invoice</h1>
                            </td>
                            <td>
                                Invoice #: <?php echo $invoiceData['invoice_number']; ?><br>
                                Created: <?php echo $invoiceData['date']; ?><br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                Bill To:<br>
                                <?php echo $invoiceData['bill_to']['name']; ?><br>
                                <?php echo $invoiceData['bill_to']['email']; ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>Description</td>
                <td>Price</td>
            </tr>
            <?php foreach ($invoiceData['items'] as $item) { ?>
                <tr class="item">
                    <td><?php echo $item['description']; ?></td>
                    <td>RM<?php echo number_format($item['quantity'] * $item['unit_price'], 2); ?></td>
                </tr>
            <?php } ?>
            <tr class="total">
                <td></td>
                <td>Total: RM<?php echo number_format($totals['total'], 2); ?></td>
            </tr>
        </table>
        <p><?php echo $invoiceData['notes']; ?></p>
    </div>
</body>
</html>

<?php
$html = ob_get_clean();
require '../../vendor/autoload.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('invoice.pdf');
?>
