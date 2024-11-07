<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator Dashboard</title>
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

        .welcome-card {
            background-color: #f0f0f0;
            border-radius: 8px;
            padding: 40px;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            margin-top:50px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .welcome-card img {
            width: 150px;
            height: auto;
            margin-right: 20px;
            border-radius: 8px;
        }

        .welcome-card h1 {
            margin: 0;
            font-size: 24px;
        }

        .dashboard-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px 0;
        }

        .dashboard-card h3 {
            margin: 0;
            margin-bottom: 10px;
        }

        .dashboard-card p {
            margin: 0;
            color: #7f8c8d;
        }

        .dashboard-card a {
            display: inline-block;
            margin-top: 10px;
            color: #2980b9;
            text-decoration: none;
            font-weight: bold;
        }

        .dashboard-card a:hover {
            text-decoration: underline;
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
    <h2><a href="operatorsdashboard.php">Dashboard</a></h2>
        <a href="manage_schedule.php">Manage Bus</a>
        <a href="booking.php">Booking History</a>
        <a href="manage_offers.php">Manage offers</a>
        
    </div>
    <div class="main-content">
        <div class="welcome-card">
            <img src="bus_qr.png" alt="Welcome">
            <h1>Welcome, Operator!</h1>
        </div>

        
        <div class="dashboard-card">
            <h3>Manage Bus</h3>
            
            <a href="manage_schedule.php">Go to Manage Bus</a>
        </div>
        <div class="dashboard-card">
            <h3>Booking History</h3>
            
            <a href="booking.php">Go to Booking History</a>
        </div>

        <div class="dashboard-card">
            <h3>Manage Offers</h3>
            
            <a href="manage_offers.php">Go to Manage Offers</a>
        </div>
    </div>
</body>
</html>
