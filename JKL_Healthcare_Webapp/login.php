<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header("Location: " . ($_SESSION['role'] === 'admin' ? "admin_dashboard.php" : "caregiver_dashboard.php"));
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            background-color: #000; /* Match body background */
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

        input {
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
            background-color: transparent;  
            color: #00ffff;  
            font-size: 18px;
            font-weight: bold;
            border: 2px solid #00ffff;  
            border-radius: 5px;
            cursor: pointer;
            outline: none;
            transition: all 0.3s ease;  
        }

        button:hover {
            background-color: #00ffff;  
            color: black;  
            box-shadow: 0 0 15px #00ffff, 0 0 30px #00ffff;  
            transform: scale(1.05); 
        }

        .register-link {
            margin-top: 20px;
            color: #00ffff;
            text-decoration: none;
            font-size: 14px;
        }

        .register-link:hover {
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
        <h2>Login</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <a class="register-link" href="register.php">New User? Register Now</a>
    </div>

    <footer>
        <p>&copy; <?= date("Y") ?> JKL Healthcare Services. All Rights Reserved.</p>
    </footer>
</body>
</html>
