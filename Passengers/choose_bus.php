<?php
session_start();

if (!isset($_SESSION['u_id'])) {
    echo "<script>
            alert('Please log in to access this page.');
            window.location.href = 'signin.html';
          </script>";
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $destination = isset($_POST['destination']) ? $_POST['destination'] : '';
    $busType = isset($_POST['busType']) ? $_POST['busType'] : '';

    if (empty($busType)) {
        echo "<script>
                alert('Please choose a bus type.');
                window.location.href = 'choose_bus.php?destination=$destination';
              </script>";
        exit();
    }
}
include('connection.php');

$pdo = new PDO("mysql:host=$host;dbname=$db_name", $user, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$destination = isset($_GET['destination']) ? $_GET['destination'] : '';

if (empty($destination)) {
    echo "<script>
            alert('Destination not specified.');
            window.location.href = 'booking_ticket.php';
          </script>";
    exit();
}

$sql = "SELECT DISTINCT bus_type FROM buses WHERE destination = :destination";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':destination', $destination);
$stmt->execute();
$busTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Bus Type</title>
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

        .bus-types {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }

        .bus-type-button {
            background-color: #007bff;
            color: #fff;
            padding: 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            width: 500px;
            font-size: 22px;
            transition: background-color 0.3s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .bus-type-button:hover {
            background-color: #0056b3;
        }

        .bus-type-button p {
            margin: 10px 0 0;
            font-weight: bold;
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
        <h2>Please Choose a Bus Type</h2>
        <div class="bus-types">
            <?php foreach ($busTypes as $busType): ?>
                <form action="book_ticket.php" method="POST">
                    <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination); ?>">
                    <button type="submit" name="busType" value="<?php echo htmlspecialchars($busType); ?>" class="bus-type-button">
                        <p><?php echo htmlspecialchars($busType); ?></p>
                    </button>
                </form>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
