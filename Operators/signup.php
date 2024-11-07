<?php
// Include the database connection file
include('connection.php');

// Start a session
session_start();

// Get form data and sanitize input
$firstname = trim($_POST['firstname']);
$lastname = trim($_POST['lastname']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);
$security_question = trim($_POST['security_question']);
$security_answer = trim($_POST['security_answer']);


if (empty($firstname) || empty($lastname) || empty($email) ||  empty($password) || empty($security_question) || empty($security_answer)) {
  echo "<script>
          alert('All fields are required.');
          window.location.href = 'signup.html';
        </script>";
  exit();
}

// Check if the email is already registered
$sql_check = "SELECT * FROM operators_detail WHERE Email = '$email'";
$result_check = mysqli_query($con, $sql_check);
$count_check = mysqli_num_rows($result_check);

if ($count_check > 0) {
    echo "<script>
            alert('Email already registered.');
            window.location.href = 'signup.html';
          </script>";
} else {
    // Insert the new user data into the database
    $sql_insert = "INSERT INTO operators_detail (Firstname, Lastname, Email, Password, Security_Question, Security_Answer) VALUES ('$firstname', '$lastname', '$email', '$password', '$security_question', '$security_answer')";
    if (mysqli_query($con, $sql_insert)) {
        echo "<script>
                alert('Signup successful.');
                window.location.href = 'signin.html';
              </script>";
    } else {
        echo "<script>
                alert('Error occurred during signup.');
                window.location.href = 'signup.html';
              </script>";
    }
}

// Close the database connection
mysqli_close($con);
?>
