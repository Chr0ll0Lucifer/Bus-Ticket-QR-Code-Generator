<?php
// Start the session
session_start();

// Check if the user is logged in


include('connection.php');

// Create a new PDO instance
$pdo = new PDO("mysql:host=$host;dbname=$db_name", $user, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch unique destinations
$sql = "SELECT DISTINCT destination FROM buses";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$destinations = $stmt->fetchAll(PDO::FETCH_COLUMN);
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

        .booking_container {
            background-color: #5b738b;
            padding: 20px;
            border-radius: 20px;
            margin: 20px 0;
           
        }

        .bookingform_container {
            width: 100%;
           
        }

        .form_row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
          
        }

        .form-group {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #fff;
            font-size: 20px;
            
        }

        input, select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            padding: 15px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 20px;
            font-size: 16px;
            margin-left: ;
            margin-right: 5px;
        }

        button:hover {
            background-color: #0056b3;
        }
        .logout-btn {
            position: absolute;
            right: 30px;
            top: 15px;
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
    <script>
        function validateForm() {
            const destination = document.getElementById('destination').value;
            const date = document.getElementById('date').value;
            const today = new Date().toISOString().split('T')[0];

            if (destination === 'select') {
                alert('Please select a destination.');
                return false;
            }

            if (date === '') {
                alert('Please select a date.');
                return false;
            }

            if (date < today) {
                alert('Date cannot be in the past.');
                return false;
            }

            return true;
        }

    function confirmLogout() {
    var confirmation = confirm("Are you sure you want to log out?");
    if (confirmation) {
        // If confirmed, redirect to logout.php
        window.location.href = 'logout.php';
    }
    // If cancelled, do nothing (no redirection needed)
}

    </script>
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
        <h2>Book Your Ticket</h2>
        <div class="booking_container">
            <div class="bookingform_container">
                <form action="choose_bus.php" method="GET" onsubmit="return validateForm()">
                    <div class="form_row">
                        <div class="form-group">
                            <label for="startingPoint">Starting Point:</label>
                            <select id="startingPoint" name="startingPoint">
                                <option value="KTM">Kathmandu (KTM)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="destination">Destination:</label>
                            <select id="destination" name="destination">
                                <option value="select">Select destination</option>
                                <?php foreach ($destinations as $destination): ?>
                                    <option value="<?php echo htmlspecialchars($destination); ?>">
                                        <?php echo htmlspecialchars($destination); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date">Date:</label>
                            <input type="date" id="date" name="date">
                        </div>
                        <button type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
