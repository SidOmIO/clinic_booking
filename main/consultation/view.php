<?php
  session_start();
  if(!isset($_SESSION['login']) || (!isset($_SESSION['type']))) {
    header("location: ../../index.php");
  }
  include_once("../../config.php");
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
            <h1>Your Consultations</h1>
        </header>
        
        <section>
            <div class="container">
            <h1>Table View</h1>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Appointment ID</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Doctor's Remark</th>
                        <?php 
                        // Fetch data from the database
                        if($_SESSION['type'] == "doctor"){ ?>
                            <th>Action</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        // Fetch data from the database
                        if($_SESSION['type'] == "patient"){
                            $stmt = $mysqli->prepare("SELECT * FROM consultation c JOIN appointment a on a.id=c.appointment_id WHERE patient_email = ?");
                            $stmt->bind_param("s", $_SESSION['login']);
                        }
                        else
                            $stmt = $mysqli->prepare("SELECT a.id, a.email, a.title, a.date, a.time, c.id as cid, c.remark, c.appointment_id 
                                                      FROM appointment a LEFT JOIN consultation c on a.id=c.appointment_id");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $count = 0;

                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while($row = $result->fetch_assoc()) {
                                ++$count;
                                echo "<tr>";
                                echo "<td>" . $count . "</td>";
                                if($_SESSION['type'] == "patient"){
                                    echo "<td>" . $row["appointment_id"] . "</td>";
                                    echo "<td>" . $row["title"] . "</td>";
                                    echo "<td>" . $row["date"] . "</td>";
                                    echo "<td>" . $row["time"] . "</td>";
                                    echo "<td>" . $row["remark"] . "</td>";
                                    echo "<td><a href='details.php?id=".$row["appointment_id"]."&email=".$row["email"]."' class='btn btn-primary'>View Details</a>";
                                } else {
                                    echo "<td>" . $row["id"] . "</td>";
                                    echo "<td>" . $row["title"] . "</td>";
                                    echo "<td>" . $row["date"] . "</td>";
                                    echo "<td>" . $row["time"] . "</td>";
                                    echo "<td>" . $row["remark"] . "</td>";
                                    if(!$row["appointment_id"] && $_SESSION['type'] == "doctor")
                                        echo "<td><a href='remark.php?id=".$row["id"]."&email=".$row["email"]."' class='btn btn-primary'>Give Remark</a>";
                                    else if($row["appointment_id"])
                                        echo "<td><a href='details.php?id=".$row["cid"]."&email=".$row["email"]."' class='btn btn-primary'>View Details</a>";
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No data available</td></tr>";
                        }
                        // Close the connection
                        $stmt->close();
                        $mysqli->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>
