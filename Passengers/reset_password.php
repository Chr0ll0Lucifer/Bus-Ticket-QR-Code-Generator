<?php
// Include the database connection file
include('connection.php');

// Start a session
session_start();

// Get the new password
$new_password = trim($_POST['new_password']);
$email = $_SESSION['user_email']; // Retrieve the email from session

// Update the password in the database
$sql = "UPDATE passengers_detail SET Password = '$new_password' WHERE Email = '$email'";
if (mysqli_query($con, $sql)) {
    echo "<script>
            alert('Password reset successfully. You can now sign in.');
            window.location.href = 'signin.html';
          </script>";
} else {
    echo "<script>
            alert('Error occurred while resetting password. Please try again.');
            window.location.href = 'retrieve_security_question.php';
          </script>";
}

// Close the database connection
mysqli_close($con);
?>
