<?php
session_start();

// Database connection 
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "aqi"; //  DB name

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['final_submit'])) {
    // Store form data in session for later use
    $_SESSION['form_data'] = $_POST;
    ?>
    <h2>Review Your Information</h2>
    <ul>
        <li><strong>Name:</strong> <?php echo htmlspecialchars($_POST['fname']); ?></li>
        <li><strong>Email:</strong> <?php echo htmlspecialchars($_POST['email']); ?></li>
        <li><strong>Gender:</strong> <?php echo htmlspecialchars($_POST['gender']); ?></li>
        <li><strong>Date of Birth:</strong> <?php echo htmlspecialchars($_POST['dob']); ?></li>
        <li><strong>Country:</strong> <?php echo htmlspecialchars($_POST['Country']); ?></li>
        <li><strong>Terms:</strong> <?php echo isset($_POST['terms']) ? "Agreed" : "Not Agreed"; ?></li>
        <li><strong>Opinion:</strong> <?php echo htmlspecialchars($_POST['opinion']); ?></li>
    </ul>
    <form method="post">
        <button type="submit" name="final_submit" value="confirm">Confirm</button>
        <button type="submit" name="final_submit" value="cancel">Cancel</button>
    </form>
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
        $stmt = $conn->prepare("INSERT INTO user (Name, Email, Gender, Dob, Country, Opinion,password) VALUES (?, ?, ?, ?, ?, ?,?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param(
            "sssssss",
            $data['fname'],      //  Name
            $data['email'],
            $data['gender'],
            $data['dob'],
            $data['Country'],
            $data['opinion'],
             $data['cpassword']
        );
        if ($stmt->execute()) {
            unset($_SESSION['form_data']);
            // Redirect to nindex.html (login section) after successful registration
            session_write_close();
            header("Location: nindex.html"); // #login if have an anchor for the login section
            exit();
        } else {
            echo "<p style='color:red;'>Error saving registration: " . htmlspecialchars($stmt->error) . "</p>";
        }
        $stmt->close();
        unset($_SESSION['form_data']);
    } else {
        // Cancel: redirect back to registration form directly
        unset($_SESSION['form_data']);
        header("Location: nindex.html");
        exit();
    }
} else {
    echo "Invalid Request";
}

$conn->close();
?>
