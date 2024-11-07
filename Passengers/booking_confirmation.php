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

// Assuming you have the bus ID and selected seats stored in session or passed through POST
// Retrieve the bus ID and selected seats
$busId = $_POST['bus_id'] ?? null; // Ensure you get the bus_id from the previous page
$selectedSeats = explode(',', $_POST['selected_seats'] ?? ''); // Get selected seats from previous page
$userId = $_SESSION['u_id']; // Assuming user ID is stored in the session

include('connection.php');

// Fetch bus details from the database
$sqlBusDetails = "SELECT buses.bus_name, buses.destination, buses.arrival_time, buses.departure_time, buses.price 
                  FROM buses 
                  WHERE buses.bus_id = '$busId'";
$result = mysqli_query($con, $sqlBusDetails);

if (mysqli_num_rows($result) > 0) {
    $busDetails = mysqli_fetch_assoc($result);
} else {
    echo "No bus details found.";
    exit();
}


// Calculate total price based on selected seats
$pricePerSeat = $busDetails['price'];
$totalPrice = $pricePerSeat * count($selectedSeats);

$currentDate = date('Y-m-d');

// Query to get valid offers sorted by discount percentage
$sqlOffers = "SELECT offer_id, offer_name, offer_percentage 
              FROM offers 
              WHERE valid_from <= '$currentDate' AND valid_until >= '$currentDate' 
              ORDER BY offer_percentage DESC";
$resultOffers = mysqli_query($con, $sqlOffers);


// Display the booking summary and ask for confirmation

    ?>
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Book Your Ticket</title>
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
                margin-left:0px;
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
                text-align: center;
                font-size: 16px;
                transition: background-color 0.3s;
            }

            button:hover {
                background-color: #0056b3;
            }

            select{
                width: ;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
            }
            
</style>
    </head>
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
            <h2>Booking Summary</h2>
            <p><strong>Passenger ID:</strong> <?php echo $userId; ?></p>
            <p><strong>Bus Name:</strong> <?php echo $busDetails['bus_name']; ?></p>
            <p><strong>Destination:</strong> <?php echo $busDetails['destination']; ?></p>
            <p><strong>Arrival Time:</strong> <?php echo $busDetails['arrival_time']; ?></p>
            <p><strong>Departure Time:</strong> <?php echo $busDetails['departure_time']; ?></p>
            <p><strong>Booking Date:</strong> <?php echo date('Y-m-d'); ?></p>
            <p><strong>Travel Date:</strong> <?php echo date('Y-m-d', strtotime('+1 day')); ?></p>
            <p><strong>Selected Seats:</strong> <?php echo implode(', ', $selectedSeats); ?></p>
            <p><strong>Price per Seat:</strong> <?php echo $pricePerSeat; ?></p>

            <p><strong>Total Price:</strong> <?php echo $totalPrice; ?></p>
      
            
            <form method="POST" action="booking_confirmed.php" onsubmit="return confirmBooking();">
            <!-- Offer Selection -->
            <label for="offers">Select an Offer:</label>
                <select id="offers" onchange="applyOffer()">
                <option value="">-- Select Offer --</option>
                <?php 
                    // Fetch offers from the database
                $sqlOffers = "SELECT offer_id, offer_name, offer_percentage FROM offers 
                                WHERE valid_from <= CURDATE() AND valid_until >= CURDATE()";
                $resultOffers = mysqli_query($con, $sqlOffers);

            // Check for errors in the query
            if (!$resultOffers) {
                echo "<p style='color:red;'>Error fetching offers: " . mysqli_error($conn) . "</p>";
            } else {
            // Check if offers exist
            if (mysqli_num_rows($resultOffers) > 0) {
            // Populate the dropdown with offers
            while ($offer = mysqli_fetch_assoc($resultOffers)) { ?>
                <option value="<?php echo $offer['offer_id']; ?>" data-discount="<?php echo $offer['offer_percentage']; ?>">
                    <?php echo htmlspecialchars($offer['offer_name']) . " - " . $offer['offer_percentage'] . "% off"; ?>
                </option>
                    <?php }
            } else {
            // Display a message if no offers are found
            echo "<option value='' disabled>No available offers</option>";
            }
            }
        ?>
</select>

        <!-- Final Price after Discount -->
        <p id="finalPrice"><strong>Final Price:</strong><?php echo $totalPrice; ?></p>

        <!-- Confirmation form -->
        
    <input type="hidden" name="passenger_id" value="<?php echo $userId; ?>">
    <input type="hidden" name="bus_id" value="<?php echo $busId; ?>">
    <input type="hidden" name="selected_seats" value="<?php echo implode(',', $selectedSeats); ?>">
    <input type="hidden" id="selected_offer" name="selected_offer" value="">
    <input type="hidden" id="offer_name" name="offer_name" value="">
    <input type="hidden" id="total_price" name="total_price" value="<?php echo $totalPrice; ?>">
    <input type="hidden" id="final_price" name="final_price" value="">
    <input type="hidden" name="confirm_booking" value="true">
    
    <button type="submit">Confirm Booking</button>
</form>


        </div>
    </div>
<script>
    // JavaScript confirmation for booking
    function confirmBooking() {
        // Ask the user if they're sure about confirming the booking
        const userConfirmed = confirm("Are you sure you want to confirm your booking?");
        
        // If user clicks "OK", proceed with form submission
        if (userConfirmed) {
            alert("You have successfully booked your seat!");
            return true;
        } else {
            // If user clicks "Cancel", prevent form submission
            return false;
        }
    }

    function confirmLogout() {
    var confirmation = confirm("Are you sure you want to log out?");
    if (confirmation) {
        // If confirmed, redirect to logout.php
        window.location.href = 'logout.php';
    }
    // If cancelled, do nothing (no redirection needed)
    }   

    function applyOffer() {
    const offersDropdown = document.getElementById('offers');
    const selectedOption = offersDropdown.options[offersDropdown.selectedIndex];
    const discountPercentage = selectedOption.getAttribute('data-discount') || 0;
    const basePrice = <?php echo isset($totalPrice) ? $totalPrice : 0; ?>;

    // Calculate the discount and final price
    const discountAmount = (basePrice * discountPercentage) / 100;
    const finalPrice = basePrice - discountAmount;

    // Update the displayed final price
    document.getElementById('finalPrice').innerText = "Final Price:" + finalPrice.toFixed(2);

    // Set hidden fields for the selected offer ID and calculated final price
    document.getElementById('selected_offer').value = offersDropdown.value;
    document.getElementById('offer_name').value = selectedOption.text;
    document.getElementById('final_price').value = finalPrice.toFixed(2);

    // Logging for debugging
    console.log("Base Price:", basePrice);
    console.log("Discount Percentage:", discountPercentage);
    console.log("Discount Amount:", discountAmount);
    console.log("Final Price:", finalPrice);
}
</script>
    </body>
    </html>

    <?php
    


   

?>


