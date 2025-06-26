<?php
session_start();

if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit();
}

date_default_timezone_set('Asia/Dhaka');

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "hotel_reservation";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .top-bar {
            background-color: #343a40;
            color: white;
            padding: 10px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .top-bar .username {
            font-weight: bold;
        }

        .top-bar .top-button, .top-bar a.logout {
            color: white;
            text-decoration: none;
            font-weight: bold;
            border: 1px solid white;
            padding: 5px 10px;
            border-radius: 4px;
            margin-left: 10px;
            background-color: transparent;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        .top-bar .top-button:hover, .top-bar a.logout:hover {
            background-color: white;
            color: #343a40;
        }

        .container {
            padding: 20px 30px;
        }

        .greeting-box {
            background-color: #ffffff;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .greeting-box h2, .greeting-box h3 {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #e2e8f0;
        }

        .data-section {
            display: none;
        }
    </style>

    <script>
        function showSection(sectionId) {
            document.querySelectorAll('.data-section').forEach(section => section.style.display = 'none');
            document.getElementById(sectionId).style.display = 'block';
        }

        window.onload = function () {
            showSection('revenueReport');
        };
    </script>
</head>
<body>

<div class="top-bar">
    <div class="username">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
    <div>
        <button onclick="showSection('revenueReport')" class="top-button">Revenue Report</button>
        <button onclick="showSection('customerReservations')" class="top-button">Customer Reservations</button>
        <button onclick="showSection('customerInfo')" class="top-button">Customer Information</button>
        <button onclick="showSection('managerInfo')" class="top-button">Manager Information</button>
    </div>
    <div><a href="logout.php" class="logout">Logout</a></div>
</div>

<div class="container">
    <!-- Greeting -->
    <div class="greeting-box">
        <h2>Hello, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>
        <p>Today is <?php echo date("l, F j, Y"); ?> | Current time: <?php echo date("h:i A"); ?></p>
    </div>

    <!-- Revenue Report Section -->
    <div id="revenueReport" class="data-section">
        <div class="greeting-box">
            <?php
            $currentMonth = date('m');
            $currentYear = date('Y');

            $selectedMonth = isset($_GET['month']) ? $_GET['month'] : $currentMonth;
            $selectedYear = isset($_GET['year']) ? $_GET['year'] : $currentYear;
            $monthName = date('F', mktime(0, 0, 0, $selectedMonth, 1));
            ?>
            <h2>Revenue Report</h2>
            <form method="GET" id="revenueForm" style="margin-bottom: 15px;">
                <label for="month">Select Month:</label>
                <select name="month" id="month" required onchange="document.getElementById('revenueForm').submit();">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= ($m == $selectedMonth ? 'selected' : '') ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>

                <label for="year">Select Year:</label>
                <select name="year" id="year" required onchange="document.getElementById('revenueForm').submit();">
                    <?php for ($y = 2010; $y <= date('Y'); $y++): ?>
                        <option value="<?= $y ?>" <?= ($y == $selectedYear ? 'selected' : '') ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </form>

            <h3>Showing Revenue for: <?= "$monthName $selectedYear" ?></h3>

            <?php
            $totalRevenue = 0;
            $stmt = $conn->prepare("SELECT city, SUM(price) AS total_income FROM hotel_reservinfo WHERE MONTH(CHECKIN_DATETIME) = ? AND YEAR(CHECKIN_DATETIME) = ? GROUP BY city ORDER BY city");
            $stmt->bind_param("ii", $selectedMonth, $selectedYear);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                echo "<table><tr><th>City</th><th>Total Income (৳)</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    $city = htmlspecialchars($row['city']);
                    $income = $row['total_income'];
                    $totalRevenue += $income;
                    echo "<tr><td>$city</td><td>৳" . number_format($income, 2) . "</td></tr>";
                }
                echo "<tr><th>Total Revenue</th><th>৳" . number_format($totalRevenue, 2) . "</th></tr>";
                echo "</table>";
            } else {
                echo "<p>No revenue data found for $monthName $selectedYear.</p>";
            }

            $stmt->close();
            ?>
        </div>
    </div>

    <!-- Customer Reservations Section -->
    <div id="customerReservations" class="data-section">
        <h2>Customer Reservations by City</h2>
        <?php
        $res = $conn->query("SELECT city FROM hotel_reservinfo GROUP BY city ORDER BY city");

        if ($res && $res->num_rows > 0) {
            while ($cityRow = $res->fetch_assoc()) {
                $city = $cityRow['city'];
                echo "<h3>City: " . htmlspecialchars($city) . "</h3>";

                $stmt = $conn->prepare("SELECT username, room_type, guests, CHECKIN_DATETIME, CHECKOUT_DATETIME, nights, price 
                                        FROM hotel_reservinfo 
                                        WHERE city = ? 
                                        ORDER BY CHECKIN_DATETIME DESC");
                $stmt->bind_param("s", $city);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    echo "<table>
                            <tr>
                                <th>Username</th>
                                <th>Room Type</th>
                                <th>Guests</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Nights</th>
                                <th>Total Price</th>
                            </tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['username']) . "</td>
                                <td>" . htmlspecialchars($row['room_type']) . "</td>
                                <td>" . htmlspecialchars($row['guests']) . "</td>
                                <td>" . date("Y-m-d H:i", strtotime($row['CHECKIN_DATETIME'])) . "</td>
                                <td>" . date("Y-m-d H:i", strtotime($row['CHECKOUT_DATETIME'])) . "</td>
                                <td>" . htmlspecialchars($row['nights']) . "</td>
                                <td>৳" . number_format($row['price'], 2) . "</td>
                            </tr>";
                    }
                    echo "</table><br>";
                } else {
                    echo "<p>No reservations found for this city.</p>";
                }

                $stmt->close();
            }
        } else {
            echo "<p>No cities found with reservations.</p>";
        }
        ?>
    </div>

    <!-- Customer Information Section -->
    <div id="customerInfo" class="data-section">
        <h2>Customer Information</h2>
        <?php
        $res = $conn->query("SELECT NID, NAME, EMAIL, GENDER, DOB, OPINION FROM customer ORDER BY NAME");
        if (!$res) {
            echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
        } elseif ($res->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>NID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Date of Birth</th>
                        <th>Opinion</th>
                    </tr>";
            while ($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['NID']) . "</td>
                        <td>" . htmlspecialchars($row['NAME']) . "</td>
                        <td>" . htmlspecialchars($row['EMAIL']) . "</td>
                        <td>" . htmlspecialchars($row['GENDER']) . "</td>
                        <td>" . htmlspecialchars($row['DOB']) . "</td>
                        <td>" . htmlspecialchars($row['OPINION']) . "</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No customer records found.</p>";
        }
        ?>
    </div>

    <!-- Manager Information Section -->
    <div id="managerInfo" class="data-section">
        <h2>Manager Information</h2>
        <?php
        $res = $conn->query("SELECT id, username, city FROM manager");
        if ($res && $res->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>City</th>
                    </tr>";
            while ($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['id']) . "</td>
                        <td>" . htmlspecialchars($row['username']) . "</td>
                        <td>" . htmlspecialchars($row['city']) . "</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No manager information found.</p>";
        }
        ?>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
