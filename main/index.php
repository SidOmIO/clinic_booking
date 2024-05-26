<?php
  session_start();
  if(!isset($_SESSION['login']) || (!isset($_SESSION['type']))) {
        header("location: ../../index.php");
  }
  include_once("../config.php");

  if($_SESSION['type'] == 'patient'){ 
    $stmt = $mysqli->prepare("SELECT a.date, a.time, a.title, c.id, c.payment_id, c.total_price 
                              FROM appointment a 
                              LEFT JOIN consultation c ON a.id = c.appointment_id 
                              WHERE a.email = ?
                              ORDER BY a.date, a.time");
    $stmt->bind_param("s", $_SESSION['login']);
  } else {
    $stmt = $mysqli->prepare("SELECT a.id, a.email, a.date, a.time, a.title
                              FROM appointment a 
                              LEFT JOIN consultation c ON a.id = c.appointment_id
                              WHERE c.id IS NULL
                              ORDER BY a.date, a.time");
  }
    $stmt->execute();
    $result = $stmt->get_result();

    if($_SESSION['type'] == 'patient'){ 
        $date = null;
        $time = null;
        $appointments = [];
        $payment_pending = [];

        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }

        foreach ($appointments as $appointment) {
            if (is_null($appointment['id']) && (is_null($date) || strtotime($appointment['date']) <= strtotime($date))) {
                if (is_null($time) || strtotime($appointment['time']) < strtotime($time)) {
                    $date = $appointment['date'];
                    $time = $appointment['time'];
                }
            }
            else if (!is_null($appointment['id']) && is_null($appointment['payment_id'])) {
                $payment_pending[] = $appointment;
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <link rel="stylesheet" href="../assets/css/main/index.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once('sidebar.php');?>    

    <div class="main-content">
        <header>
            <h2>Welcome <?=$_SESSION['login']?></h2>
        </header>
        <br>
        
        <section>
            <?php if ($_SESSION['type'] == 'patient') {
                    if(isset($date) && isset($time)) {
                ?>
                        <div class="remark info">
                            Your next appointment is on <?=$date?> at <?=$time?>.
                        </div>
                    <?php } else { ?>
                        <div class="remark info">
                            Your have no upcoming appointment.
                        </div>
                        <?php  } 
                        if (!empty($payment_pending)){
                    ?>
                        <div class="remark warn">
                            You have <?=count($payment_pending)?> pending payment.
                        </div>
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Consultation ID</th>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payment_pending as $pending) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($pending['id']); ?></td>
                                    <td><?php echo htmlspecialchars($pending['title']); ?></td>
                                    <td><?php echo htmlspecialchars($pending['total_price']); ?></td>
                                    <td><a href='consultation/details.php?id=<?=$pending["id"]?>&email=<?=$_SESSION['login']?>' class='btn btn-primary'>View Details</a></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                  <?php } 
                    } else if($_SESSION['type'] == 'doctor'){
                  ?>
                        <div class="remark warn">
                            You have <?=$result->num_rows?> appointments that require your remarks.
                        </div>
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Appointment ID</th>
                                    <th>Patient's Email</th>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['time']); ?></td>
                                    <td><a href="consultation/remark.php?id=<?=$row["id"]?>&email=<?=$row["email"]?>" class='btn btn-warning'>Give Remark</a></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                  <?php } ?>
        </section>
    </div>
</body>
</html>
