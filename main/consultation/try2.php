<?php
include_once("../../config.php");

// Get the consultation ID from the URL
$consultation_id = $_GET['id'];

// Fetch consultation remark and total price
$remark_sql = "SELECT remark, total_price FROM consultation WHERE id = ?";
$stmt = $mysqli->prepare($remark_sql);
$stmt->bind_param("i", $consultation_id);
$stmt->execute();
$result = $stmt->get_result();
$consultation = $result->fetch_assoc();

// Fetch prescriptions and medication details
$prescription_sql = "
    SELECT m.name as medication, m.price 
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
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor's Remark and Prescription</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .remark {
            margin-bottom: 20px;
            padding: 15px;
            border-left: 5px solid #007bff;
            background-color: #e9f7fd;
        }
        .total-price {
            font-size: 1.2em;
            text-align: right;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <?php require_once('../sidebar.php');?>
    <div class="container mt-5">
        <h2>Doctor's Remark</h2>
        <div class="remark">
            <?php echo htmlspecialchars($consultation['remark']); ?>
        </div>

        <h2>Prescription</h2>
        <div class="prescription">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Medication</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prescriptions as $item) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['medication']); ?></td>
                        <td>RM<?php echo number_format($item['price'], 2); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="total-price">
                <strong>Total Price: RM<?php echo number_format($consultation['total_price'], 2); ?></strong>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
