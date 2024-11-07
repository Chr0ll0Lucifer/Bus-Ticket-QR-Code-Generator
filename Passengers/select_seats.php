<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['u_id'])) {
    echo "<script>
            alert('Please log in to access this page.');
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

$busId = isset($_POST['bus_id']) ? $_POST['bus_id'] : '';

if (empty($busId)) {
    echo "<script>
            alert('Invalid bus selection.');
            window.location.href = 'book_ticket.php';
          </script>";
    exit();
}

// Fetch bus and seat details by joining the buses and bus_type tables
$sql = "
    SELECT buses.*, bus_type.num_seats 
    FROM buses 
    JOIN bus_type 
    ON buses.bus_type = bus_type.bus_type 
    WHERE buses.bus_id = '$busId'
";
$result = mysqli_query($conn, $sql);
$bus = mysqli_fetch_assoc($result);

if (!$bus) {
    echo "<script>
            alert('Bus not found.');
            window.location.href = 'book_ticket.php';
          </script>";
    exit();
}


// Get the current date and time
$currentDateTime = date('Y-m-d H:i:s');

// Retrieve travel date from booking and departure time from buses for the specific bus
$sql = "SELECT b.booking_id, b.travel_date, bs.departure_time 
        FROM booking b
        JOIN buses bs ON b.bus_id = bs.bus_id
        WHERE b.bus_id = '$busId' AND b.status = 'confirmed'";
$result = mysqli_query($conn, $sql);

while ($booking = mysqli_fetch_assoc($result)) {
    // Combine travel_date and departure_time to create a full travel datetime
    $travelDateTime = $booking['travel_date'] . ' ' . $booking['departure_time'];

    // Check if current date and time are past the travel datetime
    if ($currentDateTime > $travelDateTime) {
        // Update seats to 'available' for this booking
        $updateSeats = "UPDATE seats SET status = 'available' 
                        WHERE bus_id = '$busId' AND booking_id = '{$booking['booking_id']}' AND status = 'reserved'";
        mysqli_query($conn, $updateSeats);
    }
}


// Fetch seat status after updating
$sql = "SELECT seat_number, status FROM seats WHERE bus_id = '$busId'";
$result = mysqli_query($conn, $sql);
$seats = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Create an associative array to hold the status of each seat
$seatStatus = [];
foreach ($seats as $seat) {
    $seatStatus[$seat['seat_number']] = $seat['status'];
}

// Define bus layouts dynamically based on num_seats from the bus_type table
$numSeats = $bus['num_seats'];

// Layouts defined based on the bus type's number of seats
$layouts = [
    15 => [ // Layout for Micro Bus (15 seats)
        ['1A', '1B', 'D'],
        ['empty', '2A', '2B'],
        ['3A', '3B', '3C'],
        ['4A', '4B', '4C'],
        ['5A', '5B', '5C']
    ],
    25 => [ // Layout for Mini Bus (25 seats)
        ['1A', '1B', 'D'],
        ['empty', '2A', '2B', '2C'],
        ['3A', '3B', '3C', '3D'],
        ['4A', '4B', '4C', '5A'],
        ['5B', '5C', '6A', '6B', '6C', '6D']
    ],
    30 => [ // Layout for Deluxe Bus (30 seats)
        ['1A', '1B', 'D'],
        ['1C', '2A', '2B'],
        ['2C', '2D', '3A'],
        ['3B', '3C', '3D'],
        ['4A', '4B', '4C'],
        ['4D', '5A', '5B'],
        ['5C', '5D', '6A'],
        ['6B', '6C', '6D'],
        ['7A', '7B', '7C'],
        ['8A', '8B', '8C']
    ]
];

// Get layout based on num_seats
$layout = isset($layouts[$numSeats]) ? $layouts[$numSeats] : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats</title>
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

        .seat-layout {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            max-width: 600px;
            margin: auto;
            border: 2px solid #000;
            padding: 10px;
        }

        .seat {
            margin-left: 60px;
            width: 50px;
            height: 50px;
            background-color: green; /* Available seat */
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            cursor: pointer;
        }

        .seat.reserved {
            background-color: red; /* Reserved seat */
            cursor: not-allowed;
        }

        .seat.selected {
            background-color: blue; /* Selected seat */
        }

        .seat.empty {
            background-color: transparent; /* Empty space */
            border: none;
        }

        .seat.disabled {
            background-color: gray; /* Disabled seat */
            cursor: not-allowed;
        }

        .book-button {
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

        .book-button:hover {
            background-color: #0056b3;
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
        <h2>Bus No: <?php echo htmlspecialchars($bus['bus_id']); ?></h2>
        <p><strong>Bus Name:</strong> <?php echo htmlspecialchars($bus['bus_name']); ?></p>
        <p><strong>Departure Time:</strong> <?php echo htmlspecialchars($bus['departure_time']); ?></p>
        <p><strong>Arrival Time:</strong> <?php echo htmlspecialchars($bus['arrival_time']); ?></p>
        <p><strong>Price:</strong> RS <?php echo htmlspecialchars($bus['price']); ?></p>
        <form action="booking_confirmation.php" method="POST" onsubmit="return validateSeatSelection();">
    <input type="hidden" name="bus_id" value="<?php echo htmlspecialchars($bus['bus_id']); ?>">
    <input type="hidden" name="selected_seats" id="selected_seats" value="">
    <div class="seat-layout">
        <?php
        // Render the seats based on the layout
        foreach ($layout as $row) {
            foreach ($row as $seat) {
                $seatClass = 'seat';
                if ($seat === 'empty') {
                    $seatClass .= ' empty';
                    echo "<div class=\"$seatClass\"></div>"; // Render empty seats
                } else {
                    $seatNumber = $seat;
                    $reserved = isset($seatStatus[$seatNumber]) && $seatStatus[$seatNumber] === 'reserved';

                    // Disable seat 'D'
                    if ($seatNumber === 'D') {
                        $seatClass .= ' disabled';
                        echo "<div class=\"$seatClass\">$seatNumber</div>";
                    } else {
                        if ($reserved) {
                            $seatClass .= ' reserved';
                        }
                        echo "<div class=\"$seatClass\" data-seat-number=\"$seatNumber\">$seatNumber</div>";
                    }
                }
            }
        }
        ?>
    </div>
    
    <br>
    <button class="book-button" type="submit">Book Now</button>
</form>
    </div>

    <script>
        // Validate that at least one seat is selected
        function validateSeatSelection() {
    const selectedSeats = document.querySelectorAll('.seat.selected');
    const selectedSeatNumbers = Array.from(selectedSeats).map(seat => seat.getAttribute('data-seat-number'));
    
    if (selectedSeatNumbers.length === 0) {
        alert('Please select at least one seat.');
        return false;
    }

    // Set the value of the hidden input to the selected seat numbers
    document.getElementById('selected_seats').value = selectedSeatNumbers.join(',');

    return true;
}

// Add click event to available seats
document.querySelectorAll('.seat').forEach(seat => {
    seat.addEventListener('click', function () {
        // Prevent action if the seat is reserved or disabled
        if (this.classList.contains('reserved') || this.classList.contains('disabled')) {
            return; // Do nothing if the seat is reserved or disabled
        }
        
        this.classList.toggle('selected');
        // Update the selected seats value
        const selectedSeats = document.querySelectorAll('.seat.selected');
        const selectedSeatNumbers = Array.from(selectedSeats).map(seat => seat.getAttribute('data-seat-number'));
        document.getElementById('selected_seats').value = selectedSeatNumbers.join(',');
    });
});
    </script>
    
</body>
</html>
