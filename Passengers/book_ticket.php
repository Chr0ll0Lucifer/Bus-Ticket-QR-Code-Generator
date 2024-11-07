<?php
session_start();

if (!isset($_SESSION['u_id'])) {
    echo "<script>
            alert('Please log in to access this page.');
            window.location.href = 'signin.html';
          </script>";
    exit();
}

include('connection.php');

$pdo = new PDO("mysql:host=$host;dbname=$db_name", $user, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$busType = isset($_POST['busType']) ? $_POST['busType'] : '';
$destination = isset($_POST['destination']) ? $_POST['destination'] : '';

if ($busType && $destination) {
    $sql = "SELECT bus_id, bus_name, departure_time, arrival_time, price FROM buses WHERE bus_type = :busType AND destination = :destination";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':busType', $busType, PDO::PARAM_STR);
    $stmt->bindParam(':destination', $destination, PDO::PARAM_STR);
    $stmt->execute();
    $buses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "<script>
            alert('No bus type or destination selected.');
            window.location.href = 'choose_bus.php';
          </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ticket</title>
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

        .bus-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .bus-item {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .bus-item p {
            margin: 0;
            font-size: 16px;
            padding: 10px;
        }

        .view-button {
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

        .view-button:hover {
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
        <h2>Available Buses of Type: <?php echo htmlspecialchars($busType); ?></h2>
        <div class="bus-list">
            <?php if (!empty($buses)): ?>
                <?php foreach ($buses as $bus): ?>
                    <div class="bus-item">
                        <div>
                            <p><strong>Bus Name:</strong> <?php echo htmlspecialchars($bus['bus_name']); ?></p>
                            <p><strong>Departure Time:</strong> <?php echo htmlspecialchars($bus['departure_time']); ?></p>
                            <p><strong>Arrival Time:</strong> <?php echo htmlspecialchars($bus['arrival_time']); ?></p>
                            <p><strong>Price:</strong> RS <?php echo htmlspecialchars($bus['price']); ?></p>
                        </div>
                        <form action="select_seats.php" method="POST">
                            <input type="hidden" name="bus_id" value="<?php echo htmlspecialchars($bus['bus_id']); ?>">
                            <input type="hidden" name="busType" value="<?php echo htmlspecialchars($busType); ?>">
                            <button type="submit" class="view-button">View seats</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No buses available for this type and destination.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
