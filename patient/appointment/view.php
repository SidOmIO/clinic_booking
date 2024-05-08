<?php
  session_start();
  if(!isset($_SESSION['login'])) {
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
            <h1>Your Appointments</h1>
        </header>
        
        <section>
            <div class="container">
            <h1>Table View</h1>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Remark</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                            // Fetch data from the database
                        $sql = "SELECT * FROM appointment";
                        $result = $mysqli->query($sql);

                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["id"] . "</td>";
                                echo "<td>" . $row["title"] . "</td>";
                                echo "<td>" . $row["date"] . "</td>";
                                echo "<td>" . $row["time"] . "</td>";
                                echo "<td>" . $row["remark"] . "</td>";
                                echo "<td>hehe</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No data available</td></tr>";
                        }

                        // Close the connection
                        $mysqli->close();
                        ?>
                    </tbody>
                </table>
                <a href="add.php" class="btn btn-primary">Book an Appointment</a>
            </div>
        </section>
    </div>
</body>
</html>
