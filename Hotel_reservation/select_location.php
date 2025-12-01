<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include 'user_bar.php';

?>
<!-- User bar -->


<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "hotel_reservation";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all locations
$result = $conn->query("SELECT ID, CITY FROM location ORDER BY CITY ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" href="windhotel.png" type="image/png">

    <title>Select Your Location</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* ... */
        }
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            margin: 40px auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        select {
            width: 90%;
            padding: 10px;
            margin: 10px 0 20px 0;
            border-radius: 6px;
            border: 1px solid #cbd5e0;
        }
        input[type="submit"] {
            padding: 10px 30px;
            background: #667eea;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #5a67d8;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Select Hotel Location</h2>
        <form method="post" action="save_location.php">
            <label for="location">Choose a location:</label><br>
            <select name="location" id="location" required>
                <option value="">--Select One--</option>
                <?php while($row = $result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['ID']); ?>">
                        <?php echo htmlspecialchars($row['CITY']); ?>
                    </option>
                <?php endwhile; ?>
            </select><br>
            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>
<?php
$conn->close();
?>