<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle appointment scheduling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['schedule_appointment'])) {
        $stmt = $db->prepare("INSERT INTO appointments (patient_id, caregiver_id, appointment_time) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['patient_id'], $_POST['caregiver_id'], $_POST['appointment_time']]);

        // Add a notification for the caregiver
        $stmt = $db->prepare("INSERT INTO notifications (caregiver_id, message) VALUES (?, ?)");
        $stmt->execute([$_POST['caregiver_id'], "New appointment scheduled for patient ID {$_POST['patient_id']}"]);
    }
}

$patients = $db->query("SELECT * FROM patients")->fetchAll(PDO::FETCH_ASSOC);
$caregivers = $db->query("SELECT * FROM caregivers")->fetchAll(PDO::FETCH_ASSOC);
$appointments = $db->query("SELECT appointments.id, patients.name AS patient_name, caregivers.name AS caregiver_name, appointments.appointment_time FROM appointments JOIN patients ON appointments.patient_id = patients.id JOIN caregivers ON appointments.caregiver_id = caregivers.id")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Management</title>
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

        .appointment-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .appointment-list th, .appointment-list td {
            padding: 10px;
            text-align: left;
            border: 1px solid #00ffff;
        }

        .appointment-list th {
            background-color: #00ffff;
            color: #000;
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
    Appointment Management
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

        <select name="caregiver_id" required>
            <option value="" disabled selected>Select Caregiver</option>
            <?php foreach ($caregivers as $caregiver): ?>
                <option value="<?= $caregiver['id'] ?>"><?= $caregiver['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <input type="datetime-local" name="appointment_time" required>

        <button type="submit" name="schedule_appointment">Schedule</button>
    </form>

    <!-- Existing Appointments List -->
    <h3>Existing Appointments</h3>
    <div class="appointment-list">
        <table>
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Caregiver Name</th>
                    <th>Appointment Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?= $appointment['patient_name'] ?></td>
                        <td><?= $appointment['caregiver_name'] ?></td>
                        <td><?= $appointment['appointment_time'] ?></td>
                        <td>
                            <a href="edit_appointment.php?id=<?= $appointment['id'] ?>">Edit</a> | 
                            <a href="delete_appointment.php?id=<?= $appointment['id'] ?>" onclick="return confirm('Are you sure you want to delete this appointment?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

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
