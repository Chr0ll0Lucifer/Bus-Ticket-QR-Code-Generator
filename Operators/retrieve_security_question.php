<?php
// Start a session
session_start();

// Include database connection file
include('connection.php');

// Initialize variables
$security_question = "";
$email_error = "";

// Check if form was submitted
if (isset($_POST['retrieve'])) {
    $email = trim($_POST['email']);
    $sql = "SELECT Security_Question FROM operators_detail WHERE Email = '$email'";
    $result = mysqli_query($con, $sql);

    // Check if email exists in the database
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $security_question = $row['Security_Question'];
        $_SESSION['user_email'] = $email; // Store email for later use
    } else {
        $email_error = "Email not found. Please try again.";
    }
}

// Close database connection
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

</style>
<body>
<header>
    <h1>Bus Ticket QR Code Generator</h1>
</header>

<div class="container">
    <div class="form-container">
        <?php if ($security_question): ?>
            <!-- Security Question Form -->
            <h2>Security Question</h2>
            <p><strong><?php echo htmlspecialchars($security_question); ?></strong></p>
            <form method="POST" action="verify_security_answer.php">
                <label>Your Answer:</label>
                <input type="text" name="security_answer" required><br><br>
                <button type="submit">Verify Answer</button>
            </form>
        <?php else: ?>
            <!-- Email Input Form -->
            <h2>Recover Password</h2><br>
            <?php if ($email_error): ?>
                <script>
                    alert("<?php echo addslashes($email_error); ?>");
                </script>
            <?php endif; ?>
            <form method="POST" action="">
                <label>Email:</label>
                <input type="email" name="email" required><br><br><br>
                <button type="submit" name="retrieve">Retrieve Security Question</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="image-container">
    <img src="image.png" height="400px" width="800px">
</div>

</body>
</html>
