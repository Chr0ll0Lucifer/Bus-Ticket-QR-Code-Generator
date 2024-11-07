<?php
include('connection.php');

$bookingId = $_GET['booking_id'];
$sql = "SELECT status FROM booking WHERE booking_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $bookingId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $bookingDetails = $result->fetch_assoc();
    if ($bookingDetails['status'] === 'canceled') {
        echo "This QR code is no longer valid.";
    } else {
        echo "Booking is confirmed. QR code is valid.";
        // Display booking details if needed
    }
} else {
    echo "Booking not found.";
}
?>
