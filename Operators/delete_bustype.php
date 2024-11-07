<?php
include('connection.php');

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM bus_type WHERE bus_type_id=$id";
    if ($con->query($sql) === TRUE) {
        echo "<script>alert('Bus deleted successfully');</script>";
        echo "<script>window.location.href='manage_bus.php';</script>";
    } else {
        echo "Error deleting bus: " . $con->error;
    }
}

$con->close();
?>
