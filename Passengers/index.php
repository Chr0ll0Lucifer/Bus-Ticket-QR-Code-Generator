<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Ticket QR Code Generator</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<style>
    .book-now{
        padding: 15px;
        font-size: 18px;
        color: #fff;
        background-color: #007bff;
        border:none;
        cursor: pointer;
        transition: background-color 0.3s;
        border-radius:15px;
        margin-right: 5px;
        margin-left: 18px;
    
    }
    
    .book-now:hover {
        background-color: #0056b3;
    }
</style>

<body>
<header>
        <h1>Bus Ticket QR Code Generator</h1>
    </header>
    <main>
        <div class="bus-container">
            <img src="buss.png" alt="Moving Bus" class="bus">
            <div class="road"></div>
        </div>
        <div class = "button-container">
        <button class="book-now" onclick="window.location.href='signup.html'">Sign Up</button>
        <button class="book-now" onclick="window.location.href='signin.html'">Sign In</button>
        </div>
    </main>
    <script src="scripts/script.js"></script>

</body>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bus = document.querySelector('.bus');
    bus.style.left = '100%';
});
</script>
</html>
