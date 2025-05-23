<?php
session_start();
if (!isset($_SESSION['selected_countries']) || count($_SESSION['selected_countries']) != 10) {
    echo "Invalid access or not enough countries selected.";
    exit();
}

// Database connection
$con = mysqli_connect("localhost", "root", "", "AQI");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Prepare country list for SQL IN clause
$selected = array_map('mysqli_real_escape_string', array_fill(0, count($_SESSION['selected_countries']), $con), $_SESSION['selected_countries']);
$in = "'" . implode("','", $selected) . "'";

// Fetch info for selected countries
$sql = "SELECT city, country, aqi FROM INFO WHERE country IN ($in) ORDER BY country, city";
$result = mysqli_query($con, $sql);

$bgcolor = isset($_COOKIE['aqi_bgcolor']) ? $_COOKIE['aqi_bgcolor'] : '#ffffff';
?>
<!DOCTYPE html>
<html>
<head>
    <title>AQI Table</title>
    <style>
        body {
            background-color: <?php echo htmlspecialchars($bgcolor); ?>;
        }
        .aqi-table-container {
            margin: 50px auto;
            width: 60%;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
        }
        th {
            background: #eee;
        }
    </style>
</head>
<body>
    <div class="aqi-table-container">
        <h2>Selected Countries AQI Information</h2>
        <table>
            <thead>
                <tr>
                    <th>City</th>
                    <th>Country</th>
                    <th>AQI</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['city']); ?></td>
                    <td><?php echo htmlspecialchars($row['country']); ?></td>
                    <td><?php echo htmlspecialchars($row['aqi']); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
mysqli_close($con);
?>