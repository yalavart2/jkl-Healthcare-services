<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch appointment details for editing
if (isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM appointments WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appointment) {
        die("Appointment not found");
    }
}

// Handle form submission to update appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("UPDATE appointments SET patient_id = ?, caregiver_id = ?, appointment_time = ? WHERE id = ?");
    $stmt->execute([$_POST['patient_id'], $_POST['caregiver_id'], $_POST['appointment_time'], $_POST['appointment_id']]);
    header("Location: admin_dashboard.php"); // Redirect back to dashboard after update
    exit();
}

$patients = $db->query("SELECT * FROM patients")->fetchAll(PDO::FETCH_ASSOC);
$caregivers = $db->query("SELECT * FROM caregivers")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #00ffff;
            color: #000;
            text-align: center;
            padding: 20px;
            font-size: 36px;
            font-weight: bold;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.8);
        }

        h2 {
            font-size: 24px;
            color: #00ffff;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        select, input[type="datetime-local"], button {
            padding: 12px;
            font-size: 16px;
            border: 2px solid #00ffff;
            border-radius: 5px;
            background-color: transparent;
            color: #00ffff;
            outline: none;
            transition: all 0.3s ease;
        }

        select:hover, input[type="datetime-local"]:hover, button:hover {
            box-shadow: 0 0 10px #00ffff, 0 0 15px #00ffff;
        }

        button {
            cursor: pointer;
            background-color: #00ffff;
            color: #000;
            font-weight: bold;
        }

        button:hover {
            background-color: #00cc99;
            box-shadow: 0 0 15px #00ffff, 0 0 25px #00ffff;
        }

        .back-button {
            text-align: center;
            margin-top: 30px;
        }

        .back-button a {
            padding: 10px 20px;
            background-color: #00ffff;
            color: #000;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-button a:hover {
            background-color: #00cc99;
        }

        footer {
            text-align: center;
            margin-top: auto;
            padding: 10px;
            font-size: 14px;
            color: #00ffff;
        }
    </style>
</head>
<body>

<header>
    Edit Appointment
</header>

<div class="container">
    <h2>Edit Appointment Details</h2>

    <!-- Edit Appointment Form -->
    <form method="POST">
        <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">

        <!-- Patient Selection -->
        <select name="patient_id" required>
            <option value="" disabled>Select Patient</option>
            <?php foreach ($patients as $patient): ?>
                <option value="<?= $patient['id'] ?>" <?= $appointment['patient_id'] == $patient['id'] ? 'selected' : '' ?>><?= $patient['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Caregiver Selection -->
        <select name="caregiver_id" required>
            <option value="" disabled>Select Caregiver</option>
            <?php foreach ($caregivers as $caregiver): ?>
                <option value="<?= $caregiver['id'] ?>" <?= $appointment['caregiver_id'] == $caregiver['id'] ? 'selected' : '' ?>><?= $caregiver['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Appointment Time -->
        <input type="datetime-local" name="appointment_time" value="<?= date('Y-m-d\TH:i', strtotime($appointment['appointment_time'])) ?>" required>

        <!-- Submit Button -->
        <button type="submit">Update Appointment</button>
    </form>

    <!-- Back Button -->
    <div class="back-button">
        <a href="admin_dashboard.php">Back to Dashboard</a>
    </div>
</div>

<footer>
    &copy; 2024 JKL Healthcare Services
</footer>

</body>
</html>
