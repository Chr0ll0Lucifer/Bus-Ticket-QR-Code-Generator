<?php
// Include the database connection file
include('connection.php');

// Start a session
session_start();

// Get form data and sanitize input
$email = trim($_POST['email']);
$contact = trim($_POST['tel']);
$password = trim($_POST['password']);

$sql = "select * from operators_detail where Email = '$email' and  Password = '$password'";  
        $result = mysqli_query($con, $sql);  
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);  
        $count = mysqli_num_rows($result);  
          
        if($count == 1){  
            $_SESSION['uid'] = $row['p_id'];
            $_SESSION["firstname"] = $_POST["firstname"];
            echo "<h1><center> Login successful </center></h1>";  
            header('location:operatorsdashboard.php');
        }  
        else{  ?>
        <script>
            alert("Login fail");
            window.location.href = "signin.html";
        </script>
        <?php           
        }     
?>  