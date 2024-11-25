<?php
require 'db.php';
session_start();

// Check if the user is logged in and is a caregiver
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'caregiver') {
    header("Location: login.php");
    exit();
}

// Handle appointment scheduling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['schedule_appointment'])) {
        $stmt = $db->prepare("INSERT INTO appointments (patient_id, caregiver_id, appointment_time) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['patient_id'], $_SESSION['user_id'], $_POST['appointment_time']]);

        // Add a notification for the caregiver
        $stmt = $db->prepare("INSERT INTO notifications (caregiver_id, message) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], "New appointment scheduled for patient ID {$_POST['patient_id']}"]);
    }

    // Handle appointment update
    if (isset($_POST['update_appointment'])) {
        $stmt = $db->prepare("UPDATE appointments SET appointment_time = ? WHERE id = ? AND caregiver_id = ?");
        $stmt->execute([$_POST['appointment_time'], $_POST['appointment_id'], $_SESSION['user_id']]);
    }

    // Handle appointment deletion
    if (isset($_POST['delete_appointment'])) {
        $stmt = $db->prepare("DELETE FROM appointments WHERE id = ? AND caregiver_id = ?");
        $stmt->execute([$_POST['appointment_id'], $_SESSION['user_id']]);
    }
}

// Fetch all patients (for scheduling appointments)
$patients = $db->query("SELECT * FROM patients")->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing appointments for the caregiver
$stmt = $db->prepare("SELECT appointments.id, patients.name AS patient_name, appointments.appointment_time FROM appointments JOIN patients ON appointments.patient_id = patients.id WHERE appointments.caregiver_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caregiver Appointment Management</title>
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
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.8);
        }

        h2 {
            font-size: 24px;
            color: #00ffff;
        }

        h3 {
            font-size: 20px;
            color: #00ffff;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
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

        .appointment-list {
            margin-top: 20px;
        }

        .appointment-list ul {
            list-style-type: none;
            padding: 0;
        }

        .appointment-list li {
            background-color: rgba(0, 0, 0, 0.6);
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
    Caregiver Appointment Management
</header>

<div class="container">
    <!-- Schedule Appointment Form -->
    <h2>Schedule Appointment</h2>
    <form method="POST">
        <select name="patient_id" required>
            <option value="" disabled selected>Select Patient</option>
            <?php foreach ($patients as $patient): ?>
                <option value="<?= $patient['id'] ?>"><?= $patient['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <input type="datetime-local" name="appointment_time" required>

        <button type="submit" name="schedule_appointment">Schedule</button>
    </form>

    <!-- Existing Appointments List -->
    <h3>Your Appointments</h3>
    <div class="appointment-list">
        <ul>
            <?php foreach ($appointments as $appointment): ?>
                <li>
                    <span><?= $appointment['patient_name'] ?> has an appointment at <?= $appointment['appointment_time'] ?></span>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
                        <button type="submit" name="delete_appointment">Delete</button>
                    </form>

                    <!-- Appointment Update Form -->
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
                        <input type="datetime-local" name="appointment_time" value="<?= $appointment['appointment_time'] ?>" required>
                        <button type="submit" name="update_appointment">Update</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Back Button -->
    <div class="back-button">
        <a href="caregiver_dashboard.php">Back to Dashboard</a>
    </div>
</div>

<footer>
    &copy; 2024 JKL Healthcare Services
</footer>

</body>
</html>
