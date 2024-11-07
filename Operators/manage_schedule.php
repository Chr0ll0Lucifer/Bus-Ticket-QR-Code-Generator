<?php
// Include the database connection file
include('connection.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bus_name = $_POST["bus_name"];
    $bus_type = $_POST["bus_type"];
    $destination = $_POST["destination"];
    $departure_time = $_POST["departure_time"];
    $arrival_time = $_POST["arrival_time"];
    $price = $_POST["price"];

    // Insert bus information into the database
    $sql = "INSERT INTO buses (bus_name, bus_type, destination, departure_time, arrival_time, price) VALUES ('$bus_name', '$bus_type', '$destination', '$departure_time', '$arrival_time', $price)";
    if ($con->query($sql) === TRUE) {
        echo "<script>alert('New bus added successfully');</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $con->error;
    }
}

$bus_type_sql = "SELECT * FROM bus_type";
$bus_type_result = $con->query($bus_type_sql);

// Fetch buses from the database
$sql = "SELECT * FROM buses";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Buses</title>
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

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            margin-top:130px;
            margin-left:500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header h1{
            color:white;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #2980b9;
            color: #fff;
        }

        .action-buttons a {
            padding: 5px 10px;
            text-decoration: none;
            color: #fff;
            border-radius: 4px;
            margin-right: 5px;
        }

        .action-buttons .edit {
            background-color: #f39c12;
        }

        .action-buttons .delete {
            background-color: #e74c3c;
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
        <h2><a href="operatorsdashboard.php">Dashboard</a></h2>
        <a href="manage_schedule.php">Manage Bus</a>
        <a href="booking.php">Booking History</a>
        <a href="manage_offers.php">Manage offers</a>
        
    </div>
    <div class="container">
        <h1>Manage Buses</h1>
        <form method="post" onsubmit="return validateForm()">
        <label for="bus_name">Bus Name:</label>
        <input type="text" name="bus_name" required>
        <label for="bus_type">Bus Type:</label>
        <select name="bus_type" required>
            <option value="">Select Bus Type</option>
            <?php
            if ($bus_type_result->num_rows > 0) {
                while ($row = $bus_type_result->fetch_assoc()) {
                    echo "<option value='" . $row["bus_type"] . "'>" . $row["bus_type"] . "</option>";
                }
            }
            ?>
        </select>
            <label for="destination">Destination:</label>
            <input type="text" name="destination" required>
            <label for="departure_time">Departure Time:</label>
            <input type="time" name="departure_time" required>
            <label for="arrival_time">Arrival Time:</label>
            <input type="time" name="arrival_time" required>
            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" required>
            <button type="submit">Add Bus</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Destination</th>
                    <th>Departure Time</th>
                    <th>Arrival Time</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["bus_id"] . "</td>";
                        echo "<td>" . $row["bus_name"] . "</td>";
                        echo "<td>" . $row["bus_type"] . "</td>";
                        echo "<td>" . $row["destination"] . "</td>";
                        echo "<td>" . $row["departure_time"] . "</td>";
                        echo "<td>" . $row["arrival_time"] . "</td>";
                        echo "<td>" . $row["price"] . "</td>";
                        echo "<td class='action-buttons'>";
                        echo "<a href='edit_bus.php?id=" . $row["bus_id"] . "' class='edit'>Edit</a>";
                        echo "<a href='delete_bus.php?delete=" . $row["bus_id"] . "' class='delete' onclick='return confirmDelete()'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No buses found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        function confirmDelete() {
        return confirm("Are you sure you want to delete this bus type?");
    }
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
// Handle bus deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM buses WHERE bus_id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Bus deleted successfully');</script>";
        echo "<script>window.location.href='manage_schedule.php';</script>";
    } else {
        echo "Error deleting bus: " . $con->error;
    }
}

$con->close();
?>
