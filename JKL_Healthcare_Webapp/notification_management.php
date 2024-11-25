<?php
require 'db.php';
session_start();

// Ensure caregiver is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'caregiver') {
    header("Location: login.php");
    exit();
}

// Handle new notification message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_message'])) {
        $stmt = $db->prepare("INSERT INTO notifications (caregiver_id, message) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $_POST['message']]);
    }
}

// Fetch existing notifications for this caregiver (for display purposes)
$notifications = $db->query("SELECT notifications.id, caregivers.name AS caregiver_name, notifications.message, notifications.created_at FROM notifications JOIN caregivers ON notifications.caregiver_id = caregivers.id ORDER BY notifications.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caregiver Notification Management</title>
    <style>
        /* Styles similar to the previous page for consistency */
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

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        textarea, button {
            padding: 12px;
            font-size: 16px;
            border: 2px solid #00ffff;
            border-radius: 5px;
            background-color: transparent;
            color: #00ffff;
            outline: none;
            transition: all 0.3s ease;
        }

        textarea {
            resize: vertical;
            height: 150px;
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

        .notification-list {
            margin-top: 20px;
        }

        .notification-list ul {
            list-style-type: none;
            padding: 0;
        }

        .notification-list li {
            background-color: rgba(0, 0, 0, 0.6);
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
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
    Caregiver Notification Management
</header>

<div class="container">
    <!-- Add Notification Form -->
    <h2>Add New Message</h2>
    <form method="POST">
        <textarea name="message" required placeholder="Type your message..."></textarea>
        <button type="submit" name="send_message">Send Message</button>
    </form>

    <!-- Existing Notifications -->
    <h3>Your Notifications</h3>
    <div class="notification-list">
        <ul>
            <?php foreach ($notifications as $notification): ?>
                <li>
                    <span><strong>Message:</strong> <?= htmlspecialchars($notification['message']) ?></span><br>
                    <span><strong>Sent by:</strong> <?= htmlspecialchars($notification['caregiver_name']) ?></span><br>
                    <span><strong>Sent on:</strong> <?= htmlspecialchars($notification['created_at']) ?></span>
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
