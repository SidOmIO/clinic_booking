<?php
session_start();

if(!isset($_SESSION['login']) && !isset($_SESSION['type'])) {
    header("location: ../../index.php");
}

include_once("../../config.php");
require ('../../vendor/autoload.php');
include_once("../../mailer.php");
$message = include_once("../../forms/message.php");

$dotenv = Dotenv\Dotenv::createImmutable(dirname(dirname(__DIR__)));
$dotenv->load();
$stripe_secret_key = $_ENV['STRIPE_SECRET_KEY'];

\Stripe\Stripe::setApiKey($stripe_secret_key);

$consultation_id = $_GET['id'];

$remark_sql = "SELECT c.remark, c.total_price, c.payment_id, p.stripe_id, p.date, p.email, u.name FROM consultation c 
                LEFT JOIN payment p on c.payment_id = p.id 
                JOIN user u on u.email = c.patient_email WHERE c.id = ?";
$stmt = $mysqli->prepare($remark_sql);
$stmt->bind_param("i", $consultation_id);
$stmt->execute();
$result = $stmt->get_result();
$consultation_result = $result->fetch_assoc();

$prescription_sql = "
    SELECT m.name as medication, m.price, p.quantity 
    FROM prescription p
    JOIN medication m ON p.medication_id = m.id
    WHERE p.consultation_id = ?";
$stmt = $mysqli->prepare($prescription_sql);
$stmt->bind_param("i", $consultation_id);
$stmt->execute();
$result = $stmt->get_result();

$prescriptions = [];
while ($row = $result->fetch_assoc()) {
    $prescriptions[] = $row;
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor's Remark and Prescription</title>
    <link rel="stylesheet" href="../../assets/css/main/index.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once('../sidebar.php');?>
    <div class="main-content">
        <header>
            <h2>Consultation Details</h2> 
        </header>
        <br>
        <?php if(isset($_GET['session_id']) && !$consultation_result['payment_id']) {
            $session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);

            if ($session->payment_status == 'paid') {
                $consultation_result['stripe_id'] = $_GET['session_id'];
                $paid = true;
                
                $payment_stmt = $mysqli->prepare("INSERT INTO payment(consultation_id, stripe_id, date, email) VALUES (?, ?, ?, ?)");
                
                if ($payment_stmt) {
                    
                    $date = date('Y-m-d');
                    $payment_stmt->bind_param("ssss", $consultation_id, $_GET['session_id'], $date, $_SESSION['login']);
                    
                    if ($payment_stmt->execute()) {
                        $payment_id = $payment_stmt->insert_id;
                        $consultation_result['payment_id'] = $payment_id;
                        $payment_stmt2 = $mysqli->prepare("UPDATE consultation SET payment_id = ? WHERE id = ?");
                        
                        if ($payment_stmt2) {
                            $payment_stmt2->bind_param("ii", $payment_id, $consultation_id);
                            if ($payment_stmt2->execute()) {
                                sendMail($_SESSION['login'], $message['payment_title'], $message['payment_body']);
                                echo "<div class='remark info'>Payment Successful!</div>";
                            } else {
                                echo "<div class='remark warn'>Failed to update consultation with payment details.</div>";
                            }
                            $payment_stmt2->close();
                        } else {
                            echo "<div class='remark warn'>Failed to prepare consultation update statement.</div>";
                        }
                    } else {
                        echo "<div class='remark warn'>Failed to insert payment record.</div>";
                    }
                    $payment_stmt->close();
                } else {
                    echo "<div class='remark warn'>Failed to prepare payment insert statement.</div>";
                }
            }
        }
        $mysqli->close();
        ?>
        <h2>Doctor's Remark</h2>
        <div class="remark info">
            <?php echo htmlspecialchars($consultation_result['remark']); ?>
        </div>

        <h2>Prescription</h2>
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Medication</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $item) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['medication']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td>RM<?php echo number_format($item['price'], 2); ?></td>
                    <td>RM<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="total-price">
            <strong>Total Price: RM<?php echo number_format($consultation_result['total_price'], 2); ?></strong>
        </div>
        <div class="total-price">
            <strong>Payment Status :
                <?php 
                    if(!isset($_GET['session_id'])){
                        if($consultation_result['payment_id'])
                        echo "Paid";
                        else
                        echo "Not Paid"; 
                    } else {
                        if($consultation_result['stripe_id'] == $_GET['session_id'])
                            echo "Paid";
                        else
                            echo "Not Paid"; 
                    } ?></strong>
        </div>
        <?php if($_SESSION['type'] == "patient") { 
                if(!$consultation_result['payment_id'] || (isset($_GET['session_id']) && $consultation_result['stripe_id'] != $_GET['session_id'])){
            ?>
            <form action="../checkout.php" method="post" id="checkoutForm">
                <input type="hidden" name="id" value="<?=$consultation_id?>">
                <?php
                foreach ($prescriptions as $index => $item) {
                    echo '<input type="hidden" name="items[' . $index . '][name]" value="' . htmlspecialchars($item['medication']) . '">';
                    echo '<input type="hidden" name="items[' . $index . '][price]" value="' . htmlspecialchars($item['price']) . '">';
                    echo '<input type="hidden" name="items[' . $index . '][quantity]" value="' . htmlspecialchars($item['quantity']) . '">';
                }
                    echo '<button type="submit" class="btn btn-primary">Pay Now</button>';
                ?>
            </form>
        <?php } else {
            ?> 
            <form action="invoice.php" method="post" id="invoiceForm">
            <input type="hidden" name="invNo" value="<?=$consultation_result['payment_id']?>">
            <input type="hidden" name="date" value="<?=$consultation_result['date']?>">
            <input type="hidden" name="email" value="<?=$consultation_result['email']?>">
            <input type="hidden" name="name" value="<?=$consultation_result['name']?>">
            <?php
            foreach ($prescriptions as $index => $item) {
                echo '<input type="hidden" name="items[' . $index . '][name]" value="' . htmlspecialchars($item['medication']) . '">';
                echo '<input type="hidden" name="items[' . $index . '][price]" value="' . htmlspecialchars($item['price']) . '">';
                echo '<input type="hidden" name="items[' . $index . '][quantity]" value="' . htmlspecialchars($item['quantity']) . '">';
            }
                echo '<button type="submit" class="btn btn-primary">Download Invoice</button>';
            ?>
        </form>
    <?php } }?>
    </div>
</body>
</html>
