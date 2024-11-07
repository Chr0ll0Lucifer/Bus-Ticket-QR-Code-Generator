<?php
session_start();
include('connection.php');

// Check if the user is logged in
if (!isset($_SESSION['u_id'])) {
    echo "<script>
            alert('Please log in to access this page.');
            window.location.href = 'signin.html';
          </script>";
    exit();
}

$bookingId = $_POST['booking_id'];
$userId = $_SESSION['u_id'];

// Fetch the booking details to ensure it's valid
$sql = "SELECT * FROM booking WHERE booking_id = '$bookingId' AND p_id = '$userId'";
$result = mysqli_query($con, $sql);
$bookingDetails = mysqli_fetch_assoc($result);

if ($bookingDetails) {
    // Cancel the booking
    $sqlCancel = "UPDATE booking SET status = 'canceled' WHERE booking_id = '$bookingId'";
    mysqli_query($con, $sqlCancel);

    // Release the seats back to availability
    $sqlReleaseSeats = "UPDATE seats SET status = 'available' WHERE booking_id = '$bookingId'";
    mysqli_query($con, $sqlReleaseSeats);

    

    echo "<script>
            alert('Booking canceled successfully. Your seats have been released.');
            window.location.href = 'booking_detail.php'; // Redirect to booking details page
          </script>";

          if ($bookingDetails['status'] === 'canceled') {
            echo "<script>
                    alert('This booking has been canceled. The QR code is no longer valid.');
                    window.location.href = 'booking_detail.php';
                  </script>";
            exit();
        }
} else {
    echo "<script>
            alert('Booking not found.');
            window.location.href = 'booking_detail.php';
          </script>";
}
?>
