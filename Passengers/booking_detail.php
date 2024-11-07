<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['u_id'])) {
    echo "<script>
            alert('Please log in to access this page.');
            window.location.href = 'signin.html';
          </script>";
    exit();
}

include('connection.php');

// Create a connection


$userId = $_SESSION['u_id'];  // Fetch the logged-in user's ID

// Fetch the booking details for the logged-in user
$sqlBookingDetails = "SELECT booking.booking_id, booking.booking_date, booking.travel_date, booking.status, booking.total_price, 
                             buses.bus_name, buses.destination, buses.arrival_time, buses.departure_time 
                      FROM booking 
                      JOIN buses ON booking.bus_id = buses.bus_id 
                      WHERE booking.p_id = '$userId' 
                      ORDER BY booking.booking_date DESC, booking.booking_id DESC";

$result = mysqli_query($con, $sqlBookingDetails);

if (mysqli_num_rows($result) > 0) {
    $bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    echo "<script>alert('No bookings found.');</script>";
    $bookings = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Booking Details</title>
    <link rel="stylesheet" href="styles/style.css">
    <style>
         body {
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

        .main-content {
            flex: 1;
            padding: 20px;
            margin-left: 270px;
            margin-top: 70px;
        }

        .dashboard-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px 0;
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #333;
            color: white;
        }
    </style>
</head>
<script>
    function confirmLogout() {
    var confirmation = confirm("Are you sure you want to log out?");
    if (confirmation) {
        // If confirmed, redirect to logout.php
        window.location.href = 'logout.php';
    }
    // If cancelled, do nothing (no redirection needed)
}
</script>
<body>
<header>
    <h1>Bus Ticket QR Code Generator</h1>
    <button class="logout-btn" onclick="confirmLogout()">Logout</button>
</header>

<div class="sidebar">
    <h2><a href="passengersdashboard.php">Dashboard</a></h2>
    <a href="booking_ticket.php">Booking</a>
    <a href="booking_detail.php">Booking Detail</a>
    <a href="offer.php">Offers</a>
</div>

<div class="main-content">
    <div class="dashboard-card">
        <h2>Your Booking Details</h2>
        <?php if (!empty($bookings)) { ?>
        <table>
            <tr>
                <th>Booking ID</th>
                <th>Bus Name</th>
                <th>Destination</th>
                <th>Arrival Time</th>
                <th>Departure Time</th>
                <th>Booking Date</th>
                <th>Travel Date</th>
                <th>Status</th>
                <th>Total Price</th>
                <th>Generate QR</th>
                <th>Cancel Booking</th> 
            </tr>
            <?php foreach ($bookings as $booking) { ?>
            <tr>
                <td><?php echo $booking['booking_id']; ?></td>
                <td><?php echo $booking['bus_name']; ?></td>
                <td><?php echo $booking['destination']; ?></td>
                <td><?php echo $booking['arrival_time']; ?></td>
                <td><?php echo $booking['departure_time']; ?></td>
                <td><?php echo $booking['booking_date']; ?></td>
                <td><?php echo $booking['travel_date']; ?></td>
                <td><?php echo ucfirst($booking['status']); ?></td>
                <td><?php echo $booking['total_price']; ?></td>
                <td>
                    <?php if ($booking['status'] != 'canceled') { ?>
                    <form action="generate_qr.php" method="post">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                        <input type="submit" value="Generate QR" style="padding: 5px; font-size: 12px; width: 80px;">
                    </form>
                    <?php } else { ?>
                    <p style="color: red;">Canceled</p>
                    <?php } ?>
                </td> 
                <td>
                    <?php if ($booking['status'] != 'canceled') { ?>
                    <form method="POST" action="cancel_booking.php">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                        <input type="submit" value="Cancel" style="padding: 5px; font-size: 12px; width: 80px;">
                    </form>
                    <?php } else { ?>
                    <p style="color: red;">Canceled</p>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
            <p>No bookings available at the moment.</p>
        <?php } ?>
    </div>
</div>
</body>
</html>

<?php
// Close the connection
mysqli_close($con);
?>
