<?php
session_start();
include('connection.php');

$today = date('Y-m-d');
$con->query("DELETE FROM offers WHERE valid_until < '$today'");

// Initialize variables
$edit_mode = false;
$offer_id = $offer_name = $offer_percentage = $valid_from = $valid_until = $min_bookings = $min_seats = '';

// Handle offer creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_offer'])) {
    $offer_name = isset($_POST["offer_name"]) ? $_POST["offer_name"] : '';
    $offer_percentage = isset($_POST["offer_percentage"]) ? $_POST["offer_percentage"] : '';
    $valid_from = isset($_POST["valid_from"]) ? $_POST["valid_from"] : '';
    $valid_until = isset($_POST["valid_until"]) ? $_POST["valid_until"] : '';
    $min_bookings = !empty($_POST["min_bookings"]) ? $_POST["min_bookings"] : 0;
    

    // Perform validations
    $errors = [];
    
    if (empty($offer_name) || empty($offer_percentage) || empty($valid_from) || empty($valid_until)) {
        $errors[] = "All required fields must be filled out!";
    }
    
    if ($offer_percentage < 0) {
        $errors[] = "Offer percentage cannot be negative!";
    }

    if ($min_bookings < 0) {
        $errors[] = "Booking cannot be negative!";
    }
    
    if (strtotime($valid_from) < time()) {
        $errors[] = "Valid From date cannot be in the past!";
    }
    
    if (strtotime($valid_until) <= strtotime($valid_from)) {
        $errors[] = "Valid Until date must be greater than Valid From date!";
    }

    // If there are errors, display them
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    } else {
        // If no errors, proceed with the insert
        $sql = "INSERT INTO offers (offer_name, offer_percentage, valid_from, valid_until, min_bookings) VALUES ('$offer_name', '$offer_percentage', '$valid_from', '$valid_until', $min_bookings)";
        if ($con->query($sql) === TRUE) {
            echo "<script>alert('Offer added successfully');</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }
    }
}

// Handle offer deletion
if (isset($_POST['delete_offer'])) {
    $offer_id = $_POST['offer_id'];
    $sql = "DELETE FROM offers WHERE offer_id = $offer_id";
    if ($con->query($sql) === TRUE) {
        echo "<script>alert('Offer deleted successfully');</script>";
    } else {
        echo "Error: " . $con->error;
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_offer'])) {
    $offer_id = $_POST['offer_id'];
    header("Location: manage_offers.php?edit_offer_id=$offer_id");
    exit();

// Handle offer update
if (isset($_GET['edit_offer_id'])) {
    $edit_mode = true;
    $offer_id = $_GET['edit_offer_id'];
    
    // Fetch the offer details for the given ID
    $sqlOffer = "SELECT * FROM offers WHERE offer_id = $offer_id";
    $resultOffer = $con->query($sqlOffer);
    
    if ($resultOffer->num_rows > 0) {
        $offer = $resultOffer->fetch_assoc();
        $offer_name = $offer['offer_name'];
        $offer_percentage = $offer['offer_percentage'];
        $valid_from = $offer['valid_from'];
        $valid_until = $offer['valid_until'];
        $min_bookings = $offer['min_bookings'];
        
    }
}
}

// Handle offer update logic after editing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_offer'])) {
    $offer_id = $_POST['offer_id'];
    $offer_name = $_POST['offer_name'];
    $offer_percentage = $_POST['offer_percentage'];
    $valid_from = $_POST['valid_from'];
    $valid_until = $_POST['valid_until'];
    $min_bookings = !empty($_POST["min_bookings"]) ? $_POST["min_bookings"] : NULL;
    

    // Perform validations for update
    $errors = [];
    
    if (empty($offer_name) || empty($offer_percentage) || empty($valid_from) || empty($valid_until)) {
        $errors[] = "All required fields must be filled out!";
    }
    
    if ($offer_percentage < 0) {
        $errors[] = "Offer percentage cannot be negative!";
    }

    if ($min_bookings < 0) {
        $errors[] = "Booking cannot be negative!";
    }
    
    if (strtotime($valid_from) < time()) {
        $errors[] = "Valid From date cannot be in the past!";
    }
    
    if (strtotime($valid_until) <= strtotime($valid_from)) {
        $errors[] = "Valid Until date must be greater than Valid From date!";
    }

    // If there are errors, display them
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    } else {
        // If no errors, proceed with the update
        $sql = "UPDATE offers SET 
            offer_name = '$offer_name', 
            offer_percentage = '$offer_percentage', 
            valid_from = '$valid_from', 
            valid_until = '$valid_until'";

        if (!empty($min_bookings)) {
            $sql .= ", min_bookings = $min_bookings";
        }

        

        $sql .= " WHERE offer_id = $offer_id";

        // Execute the query
        if ($con->query($sql) === TRUE) {
            // First, output the JavaScript alert, then redirect using JavaScript after a delay
            echo "<script>
                alert('Offer updated successfully');
                window.location.href = 'manage_offers.php'; 
            </script>";
            // Remove the PHP header function
            exit(); // Ensure script execution stops
        } else {
            echo "Error: " . $con->error;
        }
    }
}

// Handle offer edit form display
if (isset($_GET['edit_offer_id'])) {
    $edit_mode = true;
    $offer_id = $_GET['edit_offer_id'];
    $sqlOffer = "SELECT * FROM offers WHERE offer_id = $offer_id";
    $resultOffer = $con->query($sqlOffer);

    if ($resultOffer->num_rows > 0) {
        $offer = $resultOffer->fetch_assoc();
        $offer_name = $offer['offer_name'];
        $offer_percentage = $offer['offer_percentage'];
        $valid_from = $offer['valid_from'];
        $valid_until = $offer['valid_until'];
        $min_bookings = $offer['min_bookings'];
        
    }
}

// Fetch all offers
$sqlOffers = "SELECT * FROM offers";
$resultOffers = $con->query($sqlOffers);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Offers</title>
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

        .manage_container {
            max-width: 850px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            margin-top: 130px;
            margin-left: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: white;
        }
        .manage_container h1{
            text-align: center;
            color: black;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }
       .edit_offer {
            background-color: #f39c12;
        }

       .delete_offer {
            background-color: #e74c3c;
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

<div class="manage_container">
   
    <h1><?php echo $edit_mode ? "Edit Offer" : "Create Offer"; ?></h1>
    <form method="POST" action="manage_offers.php">
        <?php if ($edit_mode) { ?>
            <input type="hidden" name="offer_id" value="<?php echo $offer_id; ?>">
        <?php } ?>

        <label for="offer_name">Offer Name:</label>
        <input type="text" name="offer_name" value="<?php echo $offer_name; ?>" required>

        <label for="offer_percentage">Offer Percentage (%):</label>
        <input type="number" name="offer_percentage" value="<?php echo $offer_percentage; ?>" required>

        <label for="min_bookings">Minimum Bookings:</label>
        <input type="number" id="min_bookings" name="min_bookings" value="0">

        <label for="valid_from">Valid From:</label>
        <input type="date" name="valid_from" value="<?php echo $valid_from; ?>" required>

        <label for="valid_until">Valid Until:</label>
        <input type="date" name="valid_until" value="<?php echo $valid_until; ?>" required>

        <button type="submit" name="<?php echo $edit_mode ? "update_offer" : "create_offer"; ?>">
            <?php echo $edit_mode ? "Update Offer" : "Create Offer"; ?>
        </button>
    </form>

    <h2>Existing Offers</h2>
    <table>
        <thead>
            <tr>
                <th>Offer Name</th>
                <th>Percentage (%)</th>
                <th>Valid From</th>
                <th>Valid Until</th>
                <th>Min Bookings</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($offer = $resultOffers->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $offer['offer_name']; ?></td>
                    <td><?php echo $offer['offer_percentage']; ?>%</td>
                    <td><?php echo $offer['valid_from']; ?></td>
                    <td><?php echo $offer['valid_until']; ?></td>
                    <td><?php echo $offer['min_bookings']; ?></td>
                    <td>
                    <form method="POST" style="display:inline;">
                            <input type="hidden" name="offer_id" value="<?php echo $offer['offer_id']; ?>">
                            
                                <button type="submit" name="edit_offer" class="edit_offer" >Edit</button>
                                <button type="submit" name="delete_offer" class="delete_offer">Delete</button>
                            
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
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
</html>

<?php
$con->close();
?>
