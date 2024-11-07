<?php
session_start();
require_once 'phpqrcode/qrlib.php'; // Include the PHP QR Code library
require __DIR__ . '/vendor/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/vendor/PHPMailer-master/src/SMTP.php';
require __DIR__ . '/vendor/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Check if the user is logged in
if (!isset($_SESSION['u_id'])) {
    echo "<script>
            alert('Please log in to access this page.');
            window.location.href = 'signin.html';
          </script>";
    exit();
}

$email = isset($_SESSION['email']) ? $_SESSION['email'] : null;
if (is_null($email)) {
    echo "<script>
            alert('Email not found in session. Please log in again.');
            window.location.href = 'signin.html';
          </script>";
    exit();
}

include('connection.php');

// Create a connection
$conn = mysqli_connect($host, $user, $password, $db_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$bookingId = isset($_POST['booking_id']) ? $_POST['booking_id'] : '';
if (empty($bookingId)) {
    echo "<script>
            alert('Invalid booking ID.');
            window.location.href = 'passengersdashboard.php';
          </script>";
    exit();
}

// Fetch booking details using prepared statements
$sqlBookingDetails = "SELECT booking.booking_id, booking.total_price, booking.final_price, 
                      booking.p_id, booking.bus_id, 
                      buses.bus_name, buses.destination, buses.departure_time, buses.arrival_time, 
                      GROUP_CONCAT(seats.seat_number ORDER BY seats.seat_number ASC) AS selected_seats
                      FROM booking
                      JOIN buses ON booking.bus_id = buses.bus_id
                      LEFT JOIN seats ON booking.booking_id = seats.booking_id
                      WHERE booking.booking_id = ?
                      GROUP BY booking.booking_id";

$stmt = $conn->prepare($sqlBookingDetails);
$stmt->bind_param('i', $bookingId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $bookingDetails = $result->fetch_assoc();
} else {
    echo "<script>
            alert('No booking found with this ID.');
            window.location.href = 'passengersdashboard.php';
          </script>";
    exit();
}

// Generate QR code data with additional details
$qrData = "Booking ID: " . $bookingDetails['booking_id'] . "\n" .
          "Passenger ID: " . $bookingDetails['p_id'] . "\n" .
          "Bus ID: " . $bookingDetails['bus_id'] . "\n" .
          "Bus Name: " . $bookingDetails['bus_name'] . "\n" .
          "Destination: " . $bookingDetails['destination'] . "\n" .
          "Departure Time: " . $bookingDetails['departure_time'] . "\n" .
          "Arrival Time: " . $bookingDetails['arrival_time'] . "\n" .
          "Total Price: " . $bookingDetails['total_price'] . "\n" .
          "Final Price: " . $bookingDetails['final_price'] . "\n" .
          "Selected Seats: " . $bookingDetails['selected_seats'];
          "Validation URL: http://localhost/session/Passengers/validate_qr.php?booking_id=" . $bookingDetails['booking_id'];

// Specify QR code file path
$qrDir = 'qr_codes/';
if (!file_exists($qrDir)) {
    mkdir($qrDir, 0777, true); // Create directory if it doesn't exist
}
$qrFilePath = $qrDir . $bookingId . '.png';

// Generate the QR code
if (QRcode::png($qrData, $qrFilePath, QR_ECLEVEL_L, 4) === false) {
    echo "<script>
            alert('Failed to generate QR code. Please try again.');
            window.location.href = 'passengersdashboard.php';
          </script>";
    exit();
}

// After generating the QR code, send email logic
$mail = new PHPMailer(true);
try {
    // SMTP server configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; 
    $mail->SMTPAuth = true;
    $mail->Username = 'maharjan.saliza@gmail.com'; 
    $mail->Password = 'yfit xgjw yhkx gskx'; // Consider using environment variables
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Email settings
    $mail->setFrom('maharjan.saliza@gmail.com', 'Bus Ticket System');
    $mail->addAddress($email); // Use email from session
    $mail->Subject = 'Your Bus Ticket QR Code';
    $mail->Body = 'Please find your attached  QR code for the bus ticket.';

    // Attach the QR code image
    $mail->addAttachment($qrFilePath);

    // Send the email
    $mail->send();
    echo "<script>alert('QR code has been sent to your email.');</script>";
} catch (Exception $e) {
    echo "<script>alert('Mailer Error: " . $mail->ErrorInfo . "');</script>";
}

// Display the generated QR code (if needed)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your QR Code</title>
    <link rel="stylesheet" href="styles/style.css">
    <style>
        body {
            background-color: lightblue;
            display: flex;
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
            display: flex; /* Use flexbox layout */
            justify-content: space-between; /* Space out QR code and data */
            padding: 20px;
            margin-left: 320px; /* Sidebar width */
            margin-top: 100px; /* Header height */
            margin-right: 30px;
            flex:1;
        }

        .qr-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            width:500px; /* Set a fixed width for the QR code card */
            
        }

        .data-card {
            background-color: #ffffff; /* Background for data card */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px 0;
            flex: 1; /* Take remaining space on the right */
            width: 450px; 
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
        .nav-button {
        display: inline-block;
        padding: 10px 15px; /* Vertical and horizontal padding */
        margin: 10px; /* Space between buttons */
        font-size: 14px; /* Font size */
        color: white; /* Text color */
        background-color: #007BFF; /* Button color */
        text-decoration: none; /* Remove underline from link */
        border-radius: 4px; /* Rounded corners */
        transition: background-color 0.3s; /* Smooth transition */
        }

        .nav-button:hover {
            background-color: #0056b3; /* Darker shade on hover */
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
    <div class="qr-card">
        <h2>Your QR Code</h2>
        <img src="<?php echo $qrFilePath; ?>" alt="QR Code">
        <p>Scan the QR code above to access your booking details.</p>
    </div>
    <div class="data-card">
        <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($bookingDetails['booking_id']); ?></p>
        <p><strong>Passenger ID:</strong> <?php echo htmlspecialchars($bookingDetails['p_id']); ?></p>
        <p><strong>Bus ID:</strong> <?php echo htmlspecialchars($bookingDetails['bus_id']); ?></p>
        <p><strong>Bus Name:</strong> <?php echo htmlspecialchars($bookingDetails['bus_name']); ?></p>
        <p><strong>Destination:</strong> <?php echo htmlspecialchars($bookingDetails['destination']); ?></p>
        <p><strong>Departure Time:</strong> <?php echo htmlspecialchars($bookingDetails['departure_time']); ?></p>
        <p><strong>Arrival Time:</strong> <?php echo htmlspecialchars($bookingDetails['arrival_time']); ?></p>
        <p><strong>Total Price:</strong> RS <?php echo htmlspecialchars($bookingDetails['total_price']); ?></p>
        <p><strong>Final Price:</strong> RS <?php echo htmlspecialchars($bookingDetails['final_price']); ?></p>
        <p><strong>Selected Seats:</strong> <?php echo htmlspecialchars($bookingDetails['selected_seats']); ?></p>
        <div>
            <a href="passengersdashboard.php" class="nav-button">Back to Dashboard</a>
            <a href="booking_detail.php" class="nav-button">Booking Detail</a>
        </div>
    </div>
</div>
</body>
</html>

<?php
// Close the connection
$stmt->close();
mysqli_close($conn);
?>
