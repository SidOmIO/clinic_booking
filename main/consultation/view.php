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
    <link rel="stylesheet" href="../../assets/css/main/index.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once('../sidebar.php');?>    

    <div class="main-content">
        <header>
            <h2>
                <?php if($_SESSION['type'] == "patient") 
                        echo "Your"; 
                      else 
                        echo "All";?>  Consultations</h2>
        </header>
        
        <section>
            <div class="container">
                <br>
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <?php if($_SESSION['type'] != "patient") echo "<th>Patient's Email</th>"; ?>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Time</th>
                            <?php if($_SESSION['type'] == "patient") echo "<th>Doctor's Remark</th>"; ?>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            // Fetch data from the database
                            if($_SESSION['type'] == "patient"){
                                $stmt = $mysqli->prepare("SELECT a.id, a.email, a.title, a.date, a.time, c.id as cid, c.remark, c.appointment_id
                                                          FROM consultation c 
                                                          JOIN appointment a on a.id=c.appointment_id WHERE patient_email = ?");
                                $stmt->bind_param("s", $_SESSION['login']);
                            }
                            else
                                $stmt = $mysqli->prepare("SELECT a.id, a.email, a.title, a.date, a.time, c.id as cid, c.remark, c.appointment_id 
                                                          FROM appointment a 
                                                          LEFT JOIN consultation c on a.id=c.appointment_id");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $count = 0;

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    if($_SESSION['type'] == "patient"){
                                        echo "<td>" . $row["appointment_id"] . "</td>";
                                        echo "<td>" . $row["title"] . "</td>";
                                        echo "<td>" . $row["date"] . "</td>";
                                        echo "<td>" . $row["time"] . "</td>";
                                        echo "<td>" . $row["remark"] . "</td>";
                                        echo "<td><a href='details.php?id={$row["cid"]}&email={$row["email"]}' class='btn btn-primary'>View Details</a></td>";
                                    } else {
                                        echo "<td>" . $row["id"] . "</td>";
                                        echo "<td>" . $row["email"] . "</td>";
                                        echo "<td>" . $row["title"] . "</td>";
                                        echo "<td>" . $row["date"] . "</td>";
                                        echo "<td>" . $row["time"] . "</td>";
                                        if(!$row["appointment_id"] && $_SESSION['type'] == "doctor")
                                            echo "<td><a href='remark.php?id={$row["id"]}&email={$row["email"]}' class='btn btn-warning'>Give Remark</a></td>";
                                        else if($row["appointment_id"])
                                            echo "<td><a href='details.php?id={$row["cid"]}&email={$row["email"]}' class='btn btn-primary'>View Details</a></td>";
                                    }
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No data available</td></tr>";
                            }
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
