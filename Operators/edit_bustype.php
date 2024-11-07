<?php
include('connection.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the bus type details
    $sql = "SELECT * FROM bus_type WHERE bus_type_id=$id";
    $result = $con->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $bus_type = $row['bus_type'];
        $num_seats = $row['num_seats'];
    } else {
        echo "Bus type not found.";
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $bus_type = $_POST["bus_type"];
    $num_seats = $_POST["num_seats"];
    if (empty($bus_type)) {
        echo "<script>alert('Bus type cannot be empty');</script>";
    } 
    // Validate number of seats
    elseif ($num_seats < 0) {
        echo "<script>alert('Number of seats cannot be negative');</script>";
    } else{

    // Update bus information in the database
    $sql = "UPDATE bus_type SET bus_type='$bus_type', num_seats=$num_seats WHERE bus_type_id=$id";
    if ($con->query($sql) === TRUE) {
        echo "<script>alert('Bus updated successfully');</script>";
        echo "<script>window.location.href='manage_bus.php';</script>";
    } else {
        echo "Error updating bus: " . $con->error;
    }
}
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bus Type</title>
    <style>
        body {
            background-color: lightblue;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1;
        }

        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
            top: 50px;
            left: 0;
        }

        .sidebar h2 {
            color: #ecf0f1;
            text-align: center;
            margin-bottom: 20px;
            margin-top:50px;
        }

        .sidebar a {
            display: block;
            color: #ecf0f1;
            text-decoration: none;
            padding: 10px 0;
            margin: 10px 0;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #34495e;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top:130px;
            margin-left:500px;
        }
        .container h1 {
            text-align: center;
            color: black;
        }
        
        form {
            display: flex;
            flex-direction: column;
        }
        form input {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button {
            padding: 10px;
            background-color: #2980b9;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        form button:hover {
            background-color: #3498db;
        }
        .logout-btn {
            position: absolute;
            right: 30px;
            top: 28px;
            padding: 10px;
            background-color: grey;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size:15px;
        }
        .logout-btn:hover{
            background-color:blue;
        }
    </style>
</head>
<body>
<header>
        <h1>Bus Ticket QR Code Generator</h1>
        <button class="logout-btn" onclick="confirmLogout()">Logout</button>
    </header>
    <div class="sidebar">
    <h2><a href="operatorsdashboard.php">Dashboard</a></h2>
        <a href="manage_schedule.php">Manage Bus</a>
        <a href="booking.php">Booking History</a>
        <a href="manage_offers.php">Manage offers</a>
       
    </div>
<div class="container">
    <h1>Edit Bus Type</h1>
    <form method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <label for="bus_type">Bus Type:</label>
        <input type="text" name="bus_type" value="<?php echo $bus_type; ?>" required>
        <label for="num_seats">Number of Seats:</label>
        <input type="number" name="num_seats" value="<?php echo $num_seats; ?>" required>
        <button type="submit">Update Bus</button>
    </form>
</div>
</body>
</html>
