<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'caregiver';  // Default role set to caregiver

    $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $password, $role]);
        $message = "Registration successful! <a href='login.php'>Login here</a>";
    } catch (PDOException $e) {
        $message = "Error: Username already exists.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #000; /* Full deep black background */
            color: white; /* Text color for contrast */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        header, footer {
            position: fixed;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            background-color: #000; /* Match background */
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
            background: rgba(0, 0, 0, 0.9); /* Dark background */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px #00ffff, 0 0 25px #00ffff;
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        h2 {
            color: #00ffff; /* Neon blue */
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input, select {
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background-color: #1a1a1a; /* Dark gray background */
            color: white;
            font-size: 16px;
            outline: none;
            box-shadow: 0 0 8px #00ffff inset, 0 0 15px #00ffff inset;
        }

        input::placeholder {
            color: #aaa; /* Light gray placeholder text */
        }

        input:focus {
            animation: neon-input 1.5s infinite alternate;
        }

        @keyframes neon-input {
            0% {
                box-shadow: 0 0 8px #00ffff, 0 0 15px #00ffff;
            }
            100% {
                box-shadow: 0 0 10px #00ffff, 0 0 20px #00ffff;
            }
        }

        button {
            padding: 10px;
            margin-top: 15px;
            background-color: transparent;  /* Initially transparent */
            color: #00ffff;  /* Neon color for text */
            font-size: 18px;
            font-weight: bold;
            border: 2px solid #00ffff;  /* Border matches the neon color */
            border-radius: 5px;
            cursor: pointer;
            outline: none;
            transition: all 0.3s ease;  /* Smooth transition */
        }

        button:hover {
            background-color: #00ffff;  /* Background color turns neon */
            color: black;  /* Text turns black */
            box-shadow: 0 0 15px #00ffff, 0 0 30px #00ffff;  /* Neon glowing effect */
            transform: scale(1.05);  /* Slightly enlarge the button */
        }

        .alt-action {
            margin-top: 20px;
            color: #00ffff;
        }

        .alt-action a {
            text-decoration: none;
            color: #00ffff;
            font-weight: bold;
        }

        .alt-action a:hover {
            text-shadow: 0 0 10px #00ffff;
        }

        p.error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <header>
        <h1>JKL Healthcare Services</h1>
    </header>

    <div class="container">
        <h2>Register</h2>
        <?php if (!empty($message)) echo "<p class='error'>$message</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" style="display:none"> <!-- Hidden role selection as it's fixed to 'caregiver' -->
                <option value="caregiver">Caregiver</option>
            </select>
            <button type="submit">Register</button>
        </form>
        <div class="alt-action">
            <p>Existing User? <a href="login.php">Login Now</a></p>
        </div>
    </div>

    <footer>
        <p>&copy; <?= date("Y") ?> JKL Healthcare Services. All Rights Reserved.</p>
    </footer>
</body>
</html>
