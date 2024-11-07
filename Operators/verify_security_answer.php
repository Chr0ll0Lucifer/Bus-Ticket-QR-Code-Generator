<?php
// Include the database connection file
include('connection.php');

// Start a session
session_start();

// Get the answer from the form
$security_answer = trim($_POST['security_answer']);
$email = $_SESSION['user_email']; // Retrieve the email from session

// Check the answer against the database
$sql = "SELECT Security_Answer FROM operators_detail WHERE Email = '$email'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

// Close the database connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<style>
    body { background-color: lightblue; }
    header { background-color: #333; color: #fff; text-align: center; padding: 10px; }
    .container { margin-top: 40px; margin-left: 68%; height: 520px; width: 400px; border-radius: 40px; background-color: rgb(119, 184, 241); }
    .form-container { font-size: 18px; margin-left: 20px; margin-top: 10px; }
    .form-container h2 { padding-top: 20px; text-align: center; }
    form label { display: block; padding: 5px; }
    form input { margin-left: 5px; width: 330px; padding: 6px; border-radius: 5px; border: none; }
    .form-container button { font-size: 17px; display: block; margin: 20px auto; padding: 10px; border-radius: 15px; border: none; cursor: pointer; }
    .image-container { margin-top: -35%; margin-left: 60px; }
    .image-container img { max-width: 100%; height: 530px; border-radius: 30px; }
    p { text-align: center; color: red; }
</style>
<body>
<header>
    <h1>Bus Ticket QR Code Generator</h1>
</header>

<div class="container">
    <div class="form-container">
        <?php if ($row && $row['Security_Answer'] === $security_answer): ?>
            <!-- Security Question Form -->
            <h2>Reset Your Password</h2>
            
            <form method="POST" action="reset_password.php">
                <label>New Password:</label>
                <input type="password" name="new_password" required><br><br>
                <button type="submit">Reset Password</button>
            </form>
        <?php else: ?>
            <script>
                alert('Incorrect answer. Please try again.');
                window.location.href = 'retrieve_security_question.php';
            </script>
        <?php endif; ?>
    </div>
</div>

<div class="image-container">
    <img src="image.png" height="400px" width="800px">
</div>


</body>
</html>
