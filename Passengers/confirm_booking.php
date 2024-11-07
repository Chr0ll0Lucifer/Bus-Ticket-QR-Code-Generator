<?php
session_start();

if (!isset($_SESSION['u_id'])) {
    echo "<script>
            alert('Please log in to access this page.');
            window.location.href = 'signin.html';
          </script>";
    exit();
}

include('connection.php');
$conn = mysqli_connect($host, $user, $password, $db_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $busId = $_POST['bus_id'];
    $seats = json_decode($_POST['seats'], true);

    // Update the status of selected seats to 'reserved'
    foreach ($seats as $seat) {
        $updateSeat = "UPDATE seats SET status = 'reserved' WHERE bus_id = ? AND seat_number = ?";
        $stmt = $conn->prepare($updateSeat);
        $stmt->bind_param('ss', $busId, $seat);
        if (!$stmt->execute()) {
            echo "Error reserving seat: " . $stmt->error;
        }
    }

    // Redirect to QR code generation
    echo "<script>
            alert('Seats booked successfully! Redirecting to generate QR code.');
            window.location.href = 'generate_qr.php';
          </script>";
}
?>