<?php
// Include the database connection file
include('connection.php');

// Start a session
session_start();



// Get form data and sanitize input
$email = trim($_POST['email']);
$password = trim($_POST['password']);

// Prepare SQL statement to prevent SQL injection
$sql = "SELECT * FROM passengers_detail WHERE Email = ? AND Password = ?";  
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'ss', $email, $password);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);  
$count = mysqli_num_rows($result);  

if ($count == 1) {  
    // Store passenger ID in session
    $_SESSION['u_id'] = $row['p_id']; // Use passenger_id
    $_SESSION["firstname"] = $row["firstname"]; // Assuming 'firstname' is a column in your database
    $_SESSION['email']=$row['Email'];
    echo "<h1><center>Login successful</center></h1>";  
    header('location:passengersdashboard.php');
    exit(); // Always use exit after a header redirect
} else {  
    ?>
    <script>
        alert("Login failed. Please check your credentials.");
        window.location.href = "signin.html";
    </script>
    <?php           
}     
?>
