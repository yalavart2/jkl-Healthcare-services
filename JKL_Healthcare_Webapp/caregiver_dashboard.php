<?php
session_start();
require 'db.php';

// Check if the user is logged in and is a caregiver
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'caregiver') {
    header("Location: login.php"); // Redirect to login if not logged in or not a caregiver
    exit();
}

// Fetch caregiver details (optional, if you want to display the user's name or other info)
$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caregiver Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #000; /* Deep black background */
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        header, footer {
            position: fixed;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            background-color: #000;
            color: #00ffff;
            font-size: 20px;
            text-shadow: 0 0 5px #00ffff;
        }

        header {
            top: 0;
            box-shadow: 0 0 10px #00ffff;
        }

        footer {
            bottom: 0;
            font-size: 14px;
            box-shadow: 0 0 10px #00ffff;
        }

        .container {
            background: rgba(0, 0, 0, 0.9); /* Slightly less transparent */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px #00ffff, 0 0 25px #00ffff;
            text-align: center;
            max-width: 600px;
            width: 100%;
            margin-top: 100px;
        }

        h2 {
            color: #00ffff; /* Neon blue */
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .dashboard-button {
            padding: 15px;
            margin: 10px;
            background-color: transparent;
            color: #00ffff;
            font-size: 18px;
            font-weight: bold;
            border: 2px solid #00ffff;
            border-radius: 5px;
            cursor: pointer;
            outline: none;
            transition: all 0.3s ease;
            width: 250px;
        }

        .dashboard-button:hover {
            background-color: #00ffff;
            color: black;
            box-shadow: 0 0 15px #00ffff, 0 0 30px #00ffff;
            transform: scale(1.05);
        }

        .welcome-message {
            margin-bottom: 20px;
            color: #00ffff;
        }
    </style>
</head>
<body>
    <header>
        <h1>JKL Healthcare Services</h1>
    </header>

    <div class="container">
        <h2>Welcome, <?= htmlspecialchars($user['username']) ?>!</h2>
        <p class="welcome-message">You are logged in as a caregiver. Please choose an option:</p>
        
        
        <button class="dashboard-button" onclick="window.location.href='appointment_management.php'">Appointment Management</button>
        <button class="dashboard-button" onclick="window.location.href='notification_management.php'">Notifications</button>
    </div>

    <footer>
        <p>&copy; <?= date("Y") ?> JKL Healthcare Services. All Rights Reserved.</p>
    </footer>
</body>
</html>
