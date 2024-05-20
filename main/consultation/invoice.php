<?php
// Sample data for the invoice
$invoiceData = [
    'invoice_number' => 'INV-1001',
    'date' => date('Y-m-d'),
    'due_date' => date('Y-m-d', strtotime('+30 days')),
    'bill_to' => [
        'name' => 'John Doe',
        'address' => '123 Main St, Anytown, USA',
        'email' => 'john@example.com'
    ],
    'items' => [
        ['description' => 'Web Design', 'quantity' => 1, 'unit_price' => 500],
        ['description' => 'Hosting (1 year)', 'quantity' => 1, 'unit_price' => 100],
        ['description' => 'Domain (1 year)', 'quantity' => 1, 'unit_price' => 15]
    ],
    'notes' => 'Thank you for your business!'
];

// Function to calculate totals
function calculateTotals($items) {
    $subtotal = 0;
    foreach ($items as $item) {
        $subtotal += $item['quantity'] * $item['unit_price'];
    }
    $tax = $subtotal * 0.1;  // assuming a 10% tax rate
    $total = $subtotal + $tax;

    return ['subtotal' => $subtotal, 'tax' => $tax, 'total' => $total];
}

$totals = calculateTotals($invoiceData['items']);

// Start output buffering
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
                                Due: <?php echo $invoiceData['due_date']; ?>
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
                                <?php echo $invoiceData['bill_to']['address']; ?><br>
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
                    <td>$<?php echo number_format($item['quantity'] * $item['unit_price'], 2); ?></td>
                </tr>
            <?php } ?>
            <tr class="total">
                <td></td>
                <td>Subtotal: $<?php echo number_format($totals['subtotal'], 2); ?></td>
            </tr>
            <tr class="total">
                <td></td>
                <td>Tax: $<?php echo number_format($totals['tax'], 2); ?></td>
            </tr>
            <tr class="total">
                <td></td>
                <td>Total: $<?php echo number_format($totals['total'], 2); ?></td>
            </tr>
        </table>
        <p><?php echo $invoiceData['notes']; ?></p>
    </div>
</body>
</html>

<?php
// Get the contents of the buffer and clean the buffer
$html = ob_get_clean();
return $html;
?>
