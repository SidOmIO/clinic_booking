<?php
  session_start();
  if(!isset($_SESSION['login']) && !isset($_SESSION['type'])) {
    header("location: ../../index.php");
    }
  include_once("../../config.php");
  include_once("../../mailer.php");
  
  $message = include_once("../../forms/message.php");
  
  if($_SERVER['REQUEST_METHOD'] == 'POST'){
      $doctor_email = $_SESSION['login'];
      $patient_email = mysqli_real_escape_string($mysqli,$_POST['patient_email']);
      $appointment_id = mysqli_real_escape_string($mysqli,$_POST['appointment_id']);
      $remark = mysqli_real_escape_string($mysqli,$_POST['remark']);
      $medications = $_POST['medication'];
      $quantities = $_POST['quantity'];
      $total_price = 0;

        $mysqli->begin_transaction();

        $stmt = $mysqli->prepare("INSERT INTO consultation(appointment_id, patient_email, doctor_email, remark, total_price) VALUES (?, ?, ?, ?, ?)");
        $log = $mysqli->prepare("INSERT INTO admin_log(action_type, email, timestamp) VALUES ('add_consultation', ?, NOW())");
        $stmt_med = $mysqli->prepare("INSERT INTO prescription (consultation_id, medication_id, quantity) VALUES (?, ?, ?)");

        if ($stmt && $log && $stmt_med) {
            $stmt->bind_param("ssssi", $appointment_id, $patient_email, $doctor_email, $remark, $total_price);
            $log->bind_param("s", $doctor_email);
            
            if ($stmt->execute() && $log->execute()) {
                $consultation_id = $stmt->insert_id;
                
                foreach ($medications as $index => $record) {
                    list($medication, $price) = explode('|', $record);
                    $quantity = $quantities[$index];
                    $total_price += $price * $quantity;
                    $stmt_med->bind_param("iii", $consultation_id, $medication, $quantity);
                    if (!$stmt_med->execute()) {
                        $mysqli->rollback();
                        die("Error inserting prescription: " . $stmt_med->error);
                    }
                }

                $update_stmt = $mysqli->prepare("UPDATE consultation SET total_price = ? WHERE id = ?");
                if ($update_stmt) {
                    $update_stmt->bind_param("di", $total_price, $consultation_id);
                    if ($update_stmt->execute()) {
                        $mysqli->commit();
                    } else {
                        $mysqli->rollback();
                        die("Error updating total price: " . $update_stmt->error);
                    }
                    $update_stmt->close();
                } else {
                    $mysqli->rollback();
                    die("Error preparing update statement: " . $mysqli->error);
                }
                $stmt->close();
                $stmt_med->close();
                $log->close();
                $mysqli->close();
                sendMail($patient_email, $message['consultation_title'], $message['consultation_body']);
                echo "<script>alert('Remarks added successfully!')</script>";
                echo "<script>window.location.href = 'view.php';</script>";
                exit; 
            } else {
                echo "Error: " . $stmt->error;
            }
        }
}
    $query = "SELECT id, name, price FROM medication";
    $result = $mysqli->query($query);

    $medicationOptions = "";
    $medicationArray = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $medicationOptions .= "<option value='{$row['id']}|{$row['price']}'>{$row['name']}</option>";
            $medicationArray[] = ['value' => $row['id'], 'text' => $row['name'], 'price' => $row['price']];
        }
    } else {
        $medicationOptions = "<option value=''>No medications available</option>";
        $medicationArray[] = ['value' => '', 'text' => 'No medications available'];
    }
    $mysqli->close();
  ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Page</title>
    <link rel="stylesheet" href="../../assets/css/doctor/index.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once('../sidebar.php');?>    

    <div class="main-content">
        <header>
            <h1>Book an Appointment</h1>
        </header>
        
        <section>
            <div class="container">
                <form action="" method="POST">
                <input type="hidden" id="patient_email" name="patient_email" value="<?=$_GET['email']?>">
                    <div class="form-group">
                        <label for="title">Appointment ID</label>
                        <input type="text" class="form-control" id="appointment_id" name="appointment_id" value="<?=$_GET['id']?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="remark">Remark</label>
                        <textarea class="form-control" id="remark" name="remark" required></textarea>
                    </div>
                    <div id="dropdownContainer">
                    <div class="dropdown">
                        <label for="medication">Select Medication:</label>
                        <select name="medication[]" class="medication-select">
                            <option disabled selected>Select your medicine:-</option>
                            <?php echo $medicationOptions; ?>
                        </select>
                        <label for="quantity">Quantity:</label>
                        <input type="number" name="quantity[]" min="1" value="1">
                        <button type="button" onclick="removeDropdown(this)">Remove</button>
                    </div>
                </div>
                <button type="button" onclick="addDropdown()">Add Medication</button>
                <button type="submit">Submit</button>
            </form>
            </div>
        </section>
    </div>
    <script src="../../assets/js/remarks.js"></script>
    <script>const initialOptions = <?php echo json_encode($medicationArray); ?>;</script>
</body>
</html>
