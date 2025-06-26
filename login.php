<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "hotel_reservation";

// Connect to database
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$error = "";

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['loginPassword']);

    // First check if manager is logging in
    $stmt = $conn->prepare("SELECT * FROM manager WHERE username = ? AND password = ?");
    if (!$stmt) {
        die("Prepare failed (manager): " . $conn->error);
    }
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $managerResult = $stmt->get_result();

    if ($managerResult && $managerResult->num_rows === 1) {
        $_SESSION['manager_username'] = $username;
        header("Location: manager_dashboard.php");
        exit();
    }
    $stmt->close();

    // If not manager, check if admin is logging in
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
    if (!$stmt) {
        die("Prepare failed (admin): " . $conn->error);
    }
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $adminResult = $stmt->get_result();

    if ($adminResult && $adminResult->num_rows === 1) {
        $_SESSION['admin_username'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    }
    $stmt->close();

    // If not admin, check customer
    $stmt = $conn->prepare("SELECT * FROM customer WHERE (Email = ? OR Name = ?) AND Password = ?");
    if (!$stmt) {
        die("Prepare failed (customer): " . $conn->error);
    }
    $stmt->bind_param("sss", $username, $username, $password);
    $stmt->execute();
    $customerResult = $stmt->get_result();

    if ($customerResult && $customerResult->num_rows === 1) {
        $_SESSION['username'] = $username;
        header("Location: select_location.php");
        exit();
    } else {
        $error = "Invalid credentials or not registered.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="icon" href="windhotel.png" type="image/png">
    <style>
        body {
            background: #f7f9fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .center-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 30px;
        }
        .center-logo img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .main-title {
            text-align: center;
            font-size: 2.7rem;
            font-weight: bold;
            color: #232946;
            margin-bottom: 30px;
            letter-spacing: 2px;
        }
        .login-container {
            background: #fff;
            padding: 40px 30px 30px 30px;
            border-radius: 18px;
            max-width: 420px;
            margin: 30px auto;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .login-container h2 {
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 25px;
            color: #232946;
            letter-spacing: 1px;
        }
        label {
            font-weight: 600;
            color: #232946;
            display: block;
            margin-bottom: 7px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #cbd5e0;
            border-radius: 7px;
            font-size: 1rem;
            background: #f7f9fb;
            transition: border 0.2s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border: 1.5px solid #667eea;
            outline: none;
        }
        .login-btn {
            width: 70%;
            padding: 12px;
            background: #667eea;
            color: #fff;
            border: none;
            border-radius: 7px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-right: 10px;
            transition: background 0.2s;
        }
        .login-btn:hover {
            background: #5a67d8;
        }
        .register-link {
            font-size: 1rem;
            margin-left: 5px;
        }
        .error-message {
            color: #e53e3e;
            font-size: 14px;
            margin-top: 10px;
            text-align: left;
        }
        .login-actions {
            display: flex;
            align-items: center;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="center-logo">
        <img src="windhotel.png" alt="Logo">
        <h1 style="margin: 10px;">HOTEL RESERVATION</h1>
    </div>
    <div class="login-container">
        <h2>LOGIN</h2>
        <form method="post" action="login.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter your username or email" required>

            <label for="loginPassword">Password:</label>
            <input type="password" id="loginPassword" name="loginPassword" placeholder="Enter password" required>

            <div class="login-actions">
                <input type="submit" value="Log In" class="login-btn">
                <span>or <a href="index.html" class="register-link">Register</a></span>
            </div>
            <?php if (!empty($error)): ?>
                <span class="error-message"><?php echo $error; ?></span>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
