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
            <h1>
                <?php if($_SESSION['type'] == "patient") 
                        echo "Your"; 
                      else 
                        echo "All";?> 
            Appointments</h1>
        </header>
        
        <section>
            <div class="container">
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
                        if($_SESSION['type'] == "admin")
                            $stmt = $mysqli->prepare("SELECT a.*, c.appointment_id FROM appointment a LEFT JOIN consultation c on c.appointment_id = a.id");
                        else{
                            $stmt = $mysqli->prepare("SELECT a.*, c.appointment_id FROM appointment a LEFT JOIN consultation c on c.appointment_id = a.id WHERE email = ?");
                            $stmt->bind_param("s", $_SESSION['login']);
                        }
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $count = 0;

                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while($row = $result->fetch_assoc()) {
                                ++$count;
                                echo "<tr>";
                                echo "<td>" . $count . "</td>";
                                echo "<td>" . $row["title"] . "</td>";
                                echo "<td>" . $row["date"] . "</td>";
                                echo "<td>" . $row["time"] . "</td>";
                                echo "<td>" . $row["remark"] . "</td>";
                                if(!$row["appointment_id"])
                                    echo "<td><a href='update.php?id=".$row["id"]."&email=".$row["email"]."' class='btn btn-primary'>Update</a>
                                              <a href='delete.php?id=".$row["id"]."&email=".$row["email"]."' class='btn btn-danger'>Delete</a></td>";
                                else
                                    echo "<td><a href='../consultation/details.php?id=".$row["id"]."&email=".$row["email"]."' class='btn btn-primary'>Go to Consultation Page</a></td>";
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
                <?php 
                if($_SESSION['type'] == 'patient'){
                ?>
                <a href="add.php" class="btn btn-primary">Book an Appointment</a>
                <?php } ?>
            </div>
        </section>
    </div>
</body>
</html>
