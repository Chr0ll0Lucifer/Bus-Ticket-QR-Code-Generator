<?php
include('connection.php');

// Get bus ID from URL
$id = intval($_GET['id']);

// Fetch bus details from the database
$sql = "SELECT * FROM buses WHERE bus_id = $id";
$result = $con->query($sql);

if ($result->num_rows == 1) {
    $bus = $result->fetch_assoc();
} else {
    die("Bus not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bus_type = $_POST["bus_type"];
    $destination = $_POST["destination"];
    $departure_time = $_POST["departure_time"];
    $arrival_time = $_POST["arrival_time"];
    $price = $_POST["price"]; 

    // Update bus information in the database
    $sql = "UPDATE buses SET bus_type='$bus_type', destination='$destination', departure_time='$departure_time', arrival_time='$arrival_time', price=$price WHERE bus_id=$id";
    if ($con->query($sql) === TRUE) {
        echo "<script>alert('Bus updated successfully');</script>";
        echo "<script>window.location.href='manage_schedule.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $con->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bus</title>
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

        .edit_container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            margin-top:130px;
            margin-left:500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        form input, form select {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        form input[readonly] {
            background-color: #f9f9f9;
            cursor: not-allowed;
        }

        form button {
            padding: 10px;
            background-color: #2980b9;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #3498db;
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
    <div class="edit_container">
        <h1>Edit Bus</h1>
        <form method="post" onsubmit="return validateForm()">
            <label for="bus_id">Bus ID:</label>
            <input type="text" name="bus_id" value="<?php echo htmlspecialchars($bus['bus_id']); ?>" readonly>
            <label for="bus_type">Bus Type:</label>
            <input type="text" name="bus_type" value="<?php echo htmlspecialchars($bus['bus_type']); ?>" required>
            <label for="destination">Destination:</label>
            <input type="text" name="destination" value="<?php echo htmlspecialchars($bus['destination']); ?>" required>
            <label for="departure_time">Departure Time:</label>
            <input type="time" name="departure_time" value="<?php echo htmlspecialchars($bus['departure_time']); ?>" required>
            <label for="arrival_time">Arrival Time:</label>
            <input type="time" name="arrival_time" value="<?php echo htmlspecialchars($bus['arrival_time']); ?>" required>
            <label for="price">Price:</label>
            <input type="number" name="price" value="<?php echo htmlspecialchars($bus['price']); ?>" required>
            <button type="submit">Update Bus</button>
        </form>
    </div>
    <script>
function validateForm() {
    const busName = document.querySelector('input[name="bus_name"]').value;
    const busType = document.querySelector('select[name="bus_type"]').value;
    const destination = document.querySelector('input[name="destination"]').value;
    const departureTime = document.querySelector('input[name="departure_time"]').value;
    const arrivalTime = document.querySelector('input[name="arrival_time"]').value;
    const price = parseFloat(document.querySelector('input[name="price"]').value);

    // Check if any fields are empty
    if (!busName || !busType || !destination || !departureTime || !arrivalTime || isNaN(price)) {
        alert("All fields must be filled out.");
        return false;
    }

    // Check if destination contains numbers
    if (/\d/.test(destination)) {
        alert("Destination cannot contain numbers.");
        return false;
    }

    // Check if price is valid
    if (price < 0 ) {
        alert("Price should not be negative.");
        return false;
    }
    else if(price > 30000) {
    const confirmProceed = confirm("The price you entered is NPR " + price + ". Are you sure you want to proceed with this amount?");
    if (!confirmProceed) {
        return false; // Prevent form submission
    }
}

    return true; // If all validations pass
}
</script>
</body>
</html>

<?php
$con->close();
?>
