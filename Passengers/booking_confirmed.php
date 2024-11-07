<?php
include("connection.php");

if (!isset($_SESSION['u_id'])|| !isset($_POST['final_price']) || !isset($_POST['selected_seats']) ) {
    die("Access denied. Please log in and complete the booking process.");
}

$userId = $_SESSION['u_id'];
$busId = $_POST['bus_id']; // Make sure to send this from the previous form
$totalPrice = $_POST['total_price']; // Make sure to send this from the previous form
$finalPrice = $_POST['final_price']; // Retrieved from the form
$selectedSeats = explode(',', $_POST['selected_seats']); // Assuming seats are sent as a comma-separated string

// If confirmation is received, process the booking
$bookingDate = date('Y-m-d');
$travelDate = date('Y-m-d', strtotime('+1 day')); // Example travel date set to tomorrow; adjust as needed

// Insert booking details into the bookings table
$sqlBooking = "INSERT INTO booking (p_id, bus_id, booking_date, travel_date, status, total_price, final_price) 
               VALUES ('$userId', '$busId', '$bookingDate', '$travelDate', 'confirmed', '$totalPrice', '$finalPrice')";

if (!mysqli_query($con, $sqlBooking)) {
    die("Error inserting booking: " . mysqli_error($con));
}

// Get the booking ID of the newly inserted booking
$bookingId = mysqli_insert_id($con);

// Reserve the selected seats in the seats table
foreach ($selectedSeats as $seat) {
    // Check if the seat already exists for this bus
    $sqlCheckSeat = "SELECT * FROM seats WHERE bus_id = '$busId' AND seat_number = '$seat'";
    $resultCheck = mysqli_query($con, $sqlCheckSeat);

    if (mysqli_num_rows($resultCheck) > 0) {
        // If seat exists, update its status to 'reserved'
        $sqlUpdateSeat = "UPDATE seats SET booking_id = '$bookingId', status = 'reserved' 
                          WHERE bus_id = '$busId' AND seat_number = '$seat'";
        if (!mysqli_query($con, $sqlUpdateSeat)) {
            die("Error updating seat: " . mysqli_error($con));
        }
    } else {
        // If seat doesn't exist, insert a new seat record with 'reserved' status
        $sqlInsertSeat = "INSERT INTO seats (bus_id, seat_number, booking_id, status) 
                          VALUES ('$busId', '$seat', '$bookingId', 'reserved')";
        if (!mysqli_query($con, $sqlInsertSeat)) {
            die("Error inserting seat: " . mysqli_error($con));
        }
    }
}

// Success message or redirection
echo "Booking confirmed! Your final price after discount: $" . htmlspecialchars($finalPrice);

// Optionally, you can redirect to a confirmation page or display a success message
// header("Location: confirmation_page.php"); // Uncomment if you want to redirect


// Retrieve booking details from session
$passengerId = $_POST['passenger_id'] ?? null;
$busId = $_POST['bus_id'] ?? null;
$selectedSeats = $_POST['selected_seats'] ?? null; // This should be an array or string based on your form submission

if (!$passengerId || !$busId || !$selectedSeats) {
    echo "Missing data for booking search.";
    exit;
}

$totalPrice = $_POST['total_price'] ?? 0; // Assuming $totalPrice comes from a form submission or calculation
$finalPrice = $_POST['final_price'] ?? $totalPrice; // Default to $totalPrice if final price is not provided
$selectedOfferName = $_POST['offer_name'] ?? 'No offer selected';

// Assuming selectedSeats is a comma-separated string of seat numbers (e.g., '5B,6C')
$selectedSeatArray = explode(',', $selectedSeats);

// Check if seats exist in the seats table for the provided booking details
$placeholders = implode(',', array_fill(0, count($selectedSeatArray), '?'));
$query = "
    SELECT b.booking_id, b.total_price 
    FROM booking b
    JOIN seats s ON b.booking_id = s.booking_id
    WHERE b.p_id = ? AND b.bus_id = ? AND s.seat_number IN ($placeholders)
    GROUP BY b.booking_id
    ORDER BY b.booking_date DESC 
    LIMIT 1";

$stmt = $con->prepare($query);

// Bind parameters for passengerId, busId, and each seat in the array
$params = array_merge([$passengerId, $busId], $selectedSeatArray);
$paramTypes = "ii" . str_repeat("s", count($selectedSeatArray));

// Use call_user_func_array to bind parameters dynamically
$stmt->bind_param($paramTypes, ...$params);

// Execute query and fetch results
$stmt->execute();
$stmt->bind_result($bookingId, $totalPrice);
$stmt->fetch();
$stmt->close();

if ($bookingId) {
    echo "Booking ID: $bookingId, Total Price: $totalPrice";
} else {
    echo "No booking found for the provided details.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmed</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<style>
    /* CSS styles remain the same */
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
        margin-top: 50px;
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

    button {
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #0056b3;
    }
</style>

<script>
    function confirmLogout() {
        var confirmation = confirm("Are you sure you want to log out?");
        if (confirmation) {
            window.location.href = 'logout.php';
        }
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
        <h2>Booking Confirmed</h2>
        <p>Your booking has been confirmed successfully!</p>
        <p><strong>Booking ID:</strong> <?php echo $bookingId; ?></p>
        <p><strong>Passenger ID:</strong> <?php echo $passengerId; ?></p>
        <p><strong>Bus ID:</strong> <?php echo $busId; ?></p>
        <p><strong>Selected Seats:</strong> <?php echo $selectedSeats; ?></p>
        <p><strong>Total Price:</strong> RS <?php echo $totalPrice; ?></p>
        <p><strong>Final Price:</strong> RS <?php echo $finalPrice; ?></p>
        <p><strong>Select Offer:</strong> <?php echo $selectedOfferName; ?></p>

        <!-- Link to generate QR code -->
        <form method="POST" action="generate_qr.php">
            <input type="hidden" name="booking_id" value="<?php echo $bookingId; ?>">
            <button type="submit">Generate QR Code</button>
        </form>
    </div>
</div>

<?php
// Close the database connection
if ($con) {
    mysqli_close($con);
}
?>
</body>
</html>

