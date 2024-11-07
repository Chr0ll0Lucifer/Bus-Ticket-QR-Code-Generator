<?php
// Assuming you have a connection to your database as $conn
include('connection.php');

// Check if the user is logged in (example)
if (!isset($_SESSION['u_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch passenger's total booking count
$passenger_id = $_SESSION['u_id'];
$booking_query = "SELECT COUNT(*) AS booking_count FROM booking WHERE p_id = $passenger_id";
$booking_result = $con->query($booking_query);
$booking_row = $booking_result->fetch_assoc();
$passenger_bookings = $booking_row['booking_count'];
// Ensure you set these variables at the start of your offer.php
$busId = $_GET['bus_id'] ?? null; // Get bus_id from the URL if available
$selectedSeats = isset($_POST['selected_seats']) ? explode(',', $_POST['selected_seats']) : []; // Get selected seats from POST data


// Query to get all valid offers based on current date
$sql = "SELECT * FROM offers WHERE valid_until >= CURDATE()";
$result = $con->query($sql);

$offers = [];

if ($result->num_rows > 0) {
    // Fetch all offers into an array
    while ($row = $result->fetch_assoc()) {
        // Check if the offer is festive (min_bookings = 0) or if passenger meets the min_bookings requirement
        if ($row['min_bookings'] == 0 || $passenger_bookings >= $row['min_bookings']) {
            $offers[] = $row; // Add applicable offers to the list
        }
    }

    // Bubble sort the offers by discount_percentage in descending order
    function bubbleSortOffers($offers) {
        $n = count($offers);
        for ($i = 0; $i < $n; $i++) {
            for ($j = $n - 1; $j > $i; $j--) {
                // First sort by valid_until, then by offer_percentage if valid_until is the same
                if ($offers[$j]['valid_until'] < $offers[$j - 1]['valid_until'] ||
                    ($offers[$j]['valid_until'] == $offers[$j - 1]['valid_until'] &&
                     $offers[$j]['offer_percentage'] > $offers[$j - 1]['offer_percentage'])) {
                    // Swap
                    $temp = $offers[$j];
                    $offers[$j] = $offers[$j - 1];
                    $offers[$j - 1] = $temp;
                }
            }
        }
        return $offers;
    }
    
    // Sort the offers
    $sorted_offers = bubbleSortOffers($offers);

    // Prepare the dynamic offer messages with links
    $offer_messages = '';
foreach ($sorted_offers as $offer) {
    $offer_messages .= "<div class='offer' onclick=\"location.href='booking_ticket.php?offer_id=" . $offer['offer_id'] . "&bus_id=" . urlencode($busId) . "&selected_seats=" . urlencode(implode(',', $selectedSeats)) . "'\" style='cursor: pointer;'>
                            <h1>Special Offer: " . htmlspecialchars($offer['offer_name']) . "</h1>
                            <p>We offer you <strong>" . htmlspecialchars($offer['offer_percentage']) . "%</strong> discount!</p>
                            <p>Valid from: " . htmlspecialchars($offer['valid_from']) . " to " . htmlspecialchars($offer['valid_until']) . "</p>
                            <p>Enjoy a special discount this month!</p>
                        </div>";
}
} else {
    $offer_messages = "<h1>No valid offers available.</h1>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Special Offers</title>
    <style>
        body {
            background-color: lightblue;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        /* Header styling */
        header {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1;
        }

        /* Sidebar styling */
        .sidebar {
            position: fixed;
            top: 50px;
            left: 0;
            width: 250px;
            background-color: #2c3e50;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
            color: white;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            margin-top:50px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px 0;
            margin: 10px 0;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #34495e;
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

        /* Main content */
        .main-content {
            margin-left: 270px;
            padding: 20px;
            margin-top: 70px;
        }

        /* Offer grid styling */
        .offer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); /* Auto-fit columns */
            gap: 20px;
            margin-top: 50px;
            padding: 20px;
        }

        /* Individual offer container */
        .offer {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #2ecc71;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        /* Hover effect for offers */
        .offer:hover {
            transform: scale(1.05); /* Slight zoom effect */
        }

        /* Offer content */
        .offer h1 {
            font-size: 1.5em;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .offer p {
            font-size: 1.1em;
            color: #34495e;
            line-height: 1.5;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .sidebar {
                width: 220px;
            }

            .main-content {
                margin-left: 240px;
            }

            .offer {
                width: auto;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 220px;
            }

            .offer-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
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

<header>
    <h1>Bus Ticket QR Code Generator</h1>
    <button class="logout-btn" onclick="confirmLogout()">Logout</button>
</header>

<div class="sidebar">
    <h2><a href="passengersdashboard.php" style="color: white; text-decoration: none;">Dashboard</a></h2>
    <a href="booking_ticket.php">Booking</a>
    <a href="booking_detail.php">Booking Detail</a>
    <a href="offer.php">Offers</a>
</div>

<div class="main-content">
    <!-- Dynamic Containers for each offer -->
    <div class="offer-grid">
        <?php echo $offer_messages; ?>
    </div>
</div>

</body>
</html>
