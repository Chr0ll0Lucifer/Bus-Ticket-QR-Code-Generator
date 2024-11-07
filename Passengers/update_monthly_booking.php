<?php
// update_monthly_bookings.php

include('connection.php');

// Query to count bookings grouped by month
$sql = "SELECT MONTH(booking_date) AS month_num, COUNT(*) AS booking_count 
        FROM booking 
        GROUP BY month_num";

$result = $con->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $month_num = $row["month_num"]; // Extracted month number
        $booking_count = $row["booking_count"]; // Number of bookings in that month
        
        // Update the monthly_booking table
        $update_sql = "UPDATE monthly_booking 
                       SET booking_count = booking_count + ? 
                       WHERE month_name = ?";
        
        // Prepare to get the month name based on the month number
        $month_name = date("F", mktime(0, 0, 0, $month_num, 1)); // Get month name
        
        $stmt = $con->prepare($update_sql);
        $stmt->bind_param("is", $booking_count, $month_name);

        if ($stmt->execute()) {
            echo "Booking count updated for $month_name.<br>";
        } else {
            echo "Error updating booking count for $month_name: " . $stmt->error . "<br>";
        }

        $stmt->close();
    }
} else {
    echo "No bookings found.";
}

// Close the connection
$con->close();
?>
