<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #000;
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

        .dashboard-container {
            background: rgba(0, 0, 0, 0.9);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px #00ffff, 0 0 25px #00ffff;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        h1 {
            color: #00ffff;
            text-transform: uppercase;
            margin-bottom: 30px;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        li {
            margin: 15px 0;
        }

        a {
            display: inline-block;
            padding: 12px 20px; /* Consistent padding for all buttons */
            color: #00ffff;
            background: transparent;
            border: 2px solid #00ffff;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold; /* Ensure uniform weight */
            border-radius: 5px;
            transition: all 0.3s ease;
            width: 100%; /* Make the button take full width */
            max-width: 300px; /* Optional: limit width for better readability */
        }

        a:hover {
            background-color: #00ffff;
            color: black;
            box-shadow: 0 0 15px #00ffff, 0 0 30px #00ffff;
            transform: scale(1.05);
        }

        footer {
            font-size: 14px;
            box-shadow: 0 0 5px #00ffff;
        }

    </style>
</head>
<body>
    <header>
        <h1>JKL Healthcare Services - Admin Dashboard</h1>
    </header>

    <div class="dashboard-container">
        <h1>Welcome, Admin</h1>
        <ul>
            <li><a href="patient_management.php">Patient Management</a></li>
            <li><a href="manage_caregivers.php">Manage Caregivers</a></li> 
            <li><a href="caregiver_assignment_management.php">Caregiver Assignments</a></li>
            <li><a href="appointment.php">Manage Appointments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <footer>
        <p>&copy; <?= date("Y") ?> JKL Healthcare Services. All Rights Reserved.</p>
    </footer>
</body>
</html>
