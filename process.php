<?php
session_start();

// Database connection 
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "hotel_reservation";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['final_submit'])) {
    // Save form data to session for later use
    $_SESSION['form_data'] = $_POST;
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <link rel="icon" href="windhotel.png" type="image/png">

        <title>Review Your Information</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #f9f9f9;
                margin: 0;
                padding: 0;
            }
            .form-container {
                max-width: 400px;
                margin: 60px auto;
                background: #fff;
                padding: 25px 30px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
            h2 {
                text-align: center;
                margin-bottom: 20px;
                color: #232946;
            }
            ul {
                list-style: none;
                padding: 0;
                margin: 0 0 25px 0;
            }
            ul li {
                padding: 8px 0;
                border-bottom: 1px solid #eee;
                font-size: 1rem;
            }
            ul li strong {
                display: inline-block;
                width: 110px;
                color: #3a3a3a;
            }
            form {
                text-align: center;
            }
            button {
                font-size: 1rem;
                padding: 10px 28px;
                margin: 0 10px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-weight: 600;
                transition: background-color 0.3s ease;
            }
            button[type="submit"][name="final_submit"][value="confirm"] {
                background-color: #2b6cb0;
                color: white;
            }
            button[type="submit"][name="final_submit"][value="confirm"]:hover {
                background-color: #2c5282;
            }
            button[type="submit"][name="final_submit"][value="cancel"] {
                background-color: #e53e3e;
                color: white;
            }
            button[type="submit"][name="final_submit"][value="cancel"]:hover {
                background-color: #9b2c2c;
            }
        </style>
    </head>
    <body>
        <div class="form-container">
            <h2>Review Your Information</h2>
            <ul>
                <li><strong>NID:</strong> <?php echo htmlspecialchars($_POST['nid']); ?></li>
                <li><strong>Name:</strong> <?php echo htmlspecialchars($_POST['fname']); ?></li>
                <li><strong>Email:</strong> <?php echo htmlspecialchars($_POST['email']); ?></li>
                <li><strong>Gender:</strong> <?php echo htmlspecialchars($_POST['gender']); ?></li>
                <li><strong>Date of Birth:</strong> <?php echo htmlspecialchars($_POST['dob']); ?></li>
                <li><strong>Terms:</strong> <?php echo isset($_POST['terms']) ? "Agreed" : "Not Agreed"; ?></li>
                <li><strong>Opinion:</strong> <?php echo htmlspecialchars($_POST['opinion']); ?></li>
            </ul>
            <form method="post" action="">
                <button type="submit" name="final_submit" value="confirm">Confirm</button>
                <button type="submit" name="final_submit" value="cancel">Cancel</button>
            </form>
        </div>
    </body>
    </html>
<?php
} elseif (isset($_POST['final_submit'])) {
    if ($_POST['final_submit'] === 'confirm') {
        // Save to database or process registration
        $data = $_SESSION['form_data'];

        // Set cookie for background color (expires in 30 days)
        if (isset($data['bgcolor'])) {
            setcookie('aqi_bgcolor', $data['bgcolor'], time() + (86400 * 30), "/");
        }

        // Register user in the database
        $stmt = $conn->prepare("INSERT INTO customer (NID, Name, Email, Password, Gender, Dob, Opinion) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param(
            "issssss",
            $data['nid'],
            $data['fname'],
            $data['email'],
            $data['cpassword'],
            $data['gender'],
            $data['dob'],
            $data['opinion']
            
        );
        if ($stmt->execute()) {
            unset($_SESSION['form_data']);
            session_write_close();
            header("Location: index.html");
            exit();
        } else {
            echo "<p style='color:red;'>Error saving registration: " . htmlspecialchars($stmt->error) . "</p>";
        }
        $stmt->close();
        unset($_SESSION['form_data']);
    } else {
        // Cancel: redirect back to registration form directly
        unset($_SESSION['form_data']);
        header("Location: index.html");
        exit();
    }
} else {
    echo "Invalid Request";
}

$conn->close();
?>
