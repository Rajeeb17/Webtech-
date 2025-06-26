<?php
session_start();
if (!isset($_SESSION['manager_username'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "hotel_reservation");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch manager's city
$stmt = $conn->prepare("SELECT city FROM manager WHERE username = ?");
$stmt->bind_param("s", $_SESSION['manager_username']);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$city = $row['city'] ?? 'Unknown';
$stmt->close();

$managerName = $_SESSION['manager_username'];

// Fetch reservations
$sql = "SELECT * FROM hotel_reservinfo WHERE city = ? ORDER BY CHECKIN_DATETIME";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $city);
$stmt->execute();
$reservationResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manager Dashboard</title>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .top-bar {
            background-color: #343a40;
            color: white;
            padding: 10px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
        }
        .top-bar .username {
            font-weight: bold;
        }
        .top-bar .center-button {
            flex-grow: 1;
            text-align: center;
        }
        .top-button {
            padding: 6px 12px;
            font-weight: bold;
            border: none;
            background-color: white;
            color: #343a40;
            border-radius: 5px;
            cursor: pointer;
        }
        .top-button:hover {
            background-color: #ddd;
        }
        .top-bar a.logout {
            color: white;
            text-decoration: none;
            font-weight: bold;
            border: 1px solid white;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .top-bar a.logout:hover {
            background-color: white;
            color: #343a40;
        }
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 30px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2, h3 {
            color: #333;
        }
        table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .back-button {
            margin-top: 20px;
            text-align: center;
        }
        .back-button button {
            padding: 8px 15px;
            font-weight: bold;
            background-color: #343a40;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-button button:hover {
            background-color: #555;
        }
    </style>
    <script>
        function showSection(id) {
            document.getElementById('reservationSection').style.display = 'none';
            document.getElementById('customerInfo').style.display = 'none';
            document.getElementById(id).style.display = 'block';
        }
    </script>
</head>
<body>

<div class="top-bar">
    <div class="username"><?php echo htmlspecialchars($managerName); ?></div>
    <div class="center-button">
        <button onclick="showSection('customerInfo')" class="top-button">Customer Information</button>
    </div>
    <div class="time">
        <?php date_default_timezone_set("Asia/dhaka"); echo date("d M Y h:i A"); ?>
    </div>
    <div><a href="logout.php" class="logout">Logout</a></div>
</div>

<div class="container">
    <!-- Reservation Section (default visible) -->
    <div id="reservationSection" style="display: block;">
        <h2>Reservations in <?php echo htmlspecialchars($city); ?></h2>

        <?php
        $currentDate = '';
        while ($row = $reservationResult->fetch_assoc()) {
            $checkinDate = isset($row['CHECKIN_DATETIME']) ? date("Y-m-d", strtotime($row['CHECKIN_DATETIME'])) : 'Unknown';

            if ($currentDate != $checkinDate) {
                if ($currentDate !== '') echo "</table><br>";
                $currentDate = $checkinDate;
                echo "<h3>Check-in Date: $currentDate</h3>";
                echo "<table>
                        <tr>
                            <th>Customer</th>
                            <th>Room Type</th>
                            <th>No. of Rooms</th>
                            <th>Guests</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Nights</th>
                            <th>Total Price</th>
                        </tr>";
            }

            $customer = $row['username'] ?? '[No Name]';
            $room = $row['room_type'] ?? 'N/A';
            $rooms = $row['num_rooms'] ?? '1';
            $guests = $row['guests'] ?? '?';
            $checkin = date("Y-m-d H:i", strtotime($row['CHECKIN_DATETIME']));
            $checkout = date("Y-m-d H:i", strtotime($row['CHECKOUT_DATETIME']));
            $nights = $row['nights'] ?? '-';
            $price = isset($row['price']) ? "৳" . number_format($row['price'], 2) : '₹0.00';

            echo "<tr>
                    <td>" . htmlspecialchars($customer) . "</td>
                    <td>" . htmlspecialchars($room) . "</td>
                    <td>" . htmlspecialchars($rooms) . "</td>
                    <td>" . htmlspecialchars($guests) . "</td>
                    <td>$checkin</td>
                    <td>$checkout</td>
                    <td>$nights</td>
                    <td>$price</td>
                  </tr>";
        }
        if ($currentDate !== '') echo "</table>";
        ?>
    </div>

    <!-- Customer Info Section (default hidden) -->
    <div id="customerInfo" style="display: none;">
        <h2>Customers in <?php echo htmlspecialchars($city); ?></h2>
        <?php
        $stmt = $conn->prepare("
            SELECT DISTINCT c.NID, c.NAME, c.EMAIL, c.GENDER, c.DOB, c.OPINION
            FROM customer c
            JOIN hotel_reservinfo r ON c.NAME = r.username
            WHERE r.city = ?
            ORDER BY c.NAME
        ");
        $stmt->bind_param("s", $city);
        $stmt->execute();
        $custResult = $stmt->get_result();

        if ($custResult->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>NID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>DOB</th>
                        <th>Opinion</th>
                    </tr>";
            while ($row = $custResult->fetch_assoc()) {
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
            echo "<p>No customer information found for your city.</p>";
        }
        $stmt->close();
        $conn->close();
        ?>
        <div class="back-button">
            <button onclick="showSection('reservationSection')">Back to Reservations</button>
        </div>
    </div>
</div>

</body>
</html>
