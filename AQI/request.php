<?php
session_start();

// Database connection
$con = mysqli_connect("localhost", "root", "", "aqi");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch unique countries from INFO table
$sql = "SELECT DISTINCT country FROM INFO ORDER BY country ASC";
$result = mysqli_query($con, $sql);

$countries = [];
while ($row = mysqli_fetch_assoc($result)) {
    $countries[] = $row['country'];
}

if (isset($_SESSION['user'])) {
    echo "<h3>Welcome, " . htmlspecialchars($_SESSION['user']['Name']) . 
         " <a href='logout.php' style='font-size:14px; color:red; text-decoration:none;'>LOGOUT</a></h3>";
} else {
    header("Location: nindex.html");
    exit();
}
    
// Handle form submission
$error = "";
$selected = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected = isset($_POST['countries']) ? $_POST['countries'] : [];
    if (count($selected) != 10) {
        $error = "Please select exactly 10 countries.";
    } else {
        $_SESSION['selected_countries'] = $selected;
        header("Location: showaqi.php");
        exit();
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select 10 Countries</title>
    <style>
        .checkbox-column {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-width: 250px; /* Optional: limits the width */
        }
    </style>
</head>
<body>
    <h2>Select Exactly 10 Countries</h2>
    <form method="post">
        <div class="checkbox-column" id="country-checkboxes">
            <?php foreach ($countries as $country): ?>
                <label>
                    <input type="checkbox" name="countries[]" value="<?php echo htmlspecialchars($country); ?>"
                        <?php if (in_array($country, $selected)) echo 'checked'; ?>>
                    <?php echo htmlspecialchars($country); ?>
                </label>
            <?php endforeach; ?>
        </div>
        <br>
        <input type="submit" value="Submit">
    </form>
    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('#country-checkboxes input[type="checkbox"]');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const checked = document.querySelectorAll('#country-checkboxes input[type="checkbox"]:checked');
                if (checked.length >= 10) {
                    checkboxes.forEach(box => {
                        if (!box.checked) box.disabled = true;
                    });
                } else {
                    checkboxes.forEach(box => box.disabled = false);
                }
            });
        });
        // On page load, 
        const checked = document.querySelectorAll('#country-checkboxes input[type="checkbox"]:checked');
        if (checked.length >= 10) {
            checkboxes.forEach(box => {
                if (!box.checked) box.disabled = true;
            });
        }
    });
    </script>
</body>
</html>