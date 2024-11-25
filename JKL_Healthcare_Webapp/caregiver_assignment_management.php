<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle caregiver assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['assign_caregiver'])) {
        $stmt = $db->prepare("INSERT INTO assignments (patient_id, caregiver_id) VALUES (?, ?)");
        $stmt->execute([$_POST['patient_id'], $_POST['caregiver_id']]);
    } elseif (isset($_POST['remove_assignment'])) {
        $stmt = $db->prepare("DELETE FROM assignments WHERE id = ?");
        $stmt->execute([$_POST['assignment_id']]);
    }
}

$patients = $db->query("SELECT * FROM patients")->fetchAll(PDO::FETCH_ASSOC);
$caregivers = $db->query("SELECT * FROM caregivers WHERE availability = 'available'")->fetchAll(PDO::FETCH_ASSOC);
$assignments = $db->query("SELECT assignments.id, patients.name AS patient_name, caregivers.name AS caregiver_name FROM assignments JOIN patients ON assignments.patient_id = patients.id JOIN caregivers ON assignments.caregiver_id = caregivers.id")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caregiver Assignment Management</title>
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
            padding: 15px;
            font-size: 36px;
            font-weight: bold;
        }

        .dashboard-container {
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

        select, button {
            padding: 12px;
            font-size: 16px;
            border: 2px solid #00ffff;
            border-radius: 5px;
            background-color: transparent;
            color: #00ffff;
            outline: none;
            transition: all 0.3s ease;
        }

        select:hover, button:hover {
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

        .assignment-list {
            margin-top: 20px;
        }

        .assignment-list ul {
            list-style-type: none;
            padding: 0;
        }

        .assignment-list li {
            background-color: rgba(0, 0, 0, 0.6);
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .assignment-list form {
            display: inline-block;
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
    Caregiver Assignment Management
</header>

<div class="dashboard-container">
    <!-- Assign Caregiver Form -->
    <h2>Assign Caregiver</h2>
    <form method="POST">
        <select name="patient_id" required>
            <option value="" disabled selected>Select Patient</option>
            <?php foreach ($patients as $patient): ?>
                <option value="<?= $patient['id'] ?>"><?= $patient['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <select name="caregiver_id" required>
            <option value="" disabled selected>Select Available Caregiver</option>
            <?php foreach ($caregivers as $caregiver): ?>
                <option value="<?= $caregiver['id'] ?>"><?= $caregiver['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="assign_caregiver">Assign</button>
    </form>

    <!-- Display Current Assignments -->
    <h3>Existing Assignments</h3>
    <div class="assignment-list">
        <ul>
            <?php foreach ($assignments as $assignment): ?>
                <li>
                    <span><?= $assignment['patient_name'] ?> is assigned to <?= $assignment['caregiver_name'] ?></span>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
                        <button type="submit" name="remove_assignment">Remove</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
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
