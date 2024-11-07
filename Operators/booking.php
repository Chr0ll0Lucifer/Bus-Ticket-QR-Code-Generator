<?php
include('connection.php');

// Fetch bookings from the database with the correct table name
$sql = "
    SELECT b.booking_id, b.p_id, pd.email,  
           b.booking_date, b.travel_date, b.status 
    FROM booking b 
    JOIN passengers_detail pd ON b.p_id = pd.p_id
    JOIN buses bus ON b.bus_id = bus.bus_id";

$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: lightblue;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px;
            width: 100%;
            position: fixed;
            top: 8px;
            z-index: 1;
        }
        header h1{
            color:white;
        }


        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
            top: 60px;
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
        }body {
            background-color: lightblue;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #333;
            color: #fff;
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

        .booking_container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            margin-top:130px;
            margin-left:350px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #2980b9;
            color: #fff;
        }

        .action-buttons a {
            padding: 5px 10px;
            text-decoration: none;
            color: #fff;
            border-radius: 4px;
            margin-right: 5px;
        }

        .action-buttons .edit {
            background-color: #f39c12;
        }

        .action-buttons .delete {
            background-color: #e74c3c;
        }

        .action-buttons a:hover {
            opacity: 0.8;
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
    <div class="booking_container">
        <h1>Booking History</h1>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>P_ID</th>
                    <th>Email</th>
                    <th>Booking Date</th>
                    <th>Travel Date</th>
                    <th>Status</th>
                   
                </tr>
            </thead>
            <tbody>
            <?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["booking_id"] . "</td>";
        echo "<td>" . $row["p_id"] . "</td>";
        echo "<td>" . $row["email"] . "</td>";
        echo "<td>" . $row["booking_date"] . "</td>";
        echo "<td>" . $row["travel_date"] . "</td>";
        echo "<td>" . $row["status"] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No bookings found</td></tr>";
}
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$con->close();
?>
