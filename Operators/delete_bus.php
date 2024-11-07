<?php
include('connection.php');

// Get bus ID from URL
$id = intval($_GET['delete']);

// Delete bus from the database
$sql = "DELETE FROM buses WHERE bus_id = $id";
if ($con->query($sql) === TRUE) {
    $message = "Bus deleted successfully";
} else {
    $message = "Error: " . $sql . "<br>" . $con->error;
}

$con->close();

// Use JavaScript to show an alert and then redirect
echo "<script>
        alert('$message');
        window.location.href = 'manage_schedule.php';
      </script>";
?>
