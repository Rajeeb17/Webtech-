<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Connect to DB
$conn = new mysqli("localhost", "root", "", "hotel_reservation");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT city, room_type, num_rooms, guests, nights, price, CHECKIN_DATETIME, CHECKOUT_DATETIME FROM hotel_reservinfo WHERE username = ? ORDER BY CHECKIN_DATETIME DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

include 'user_bar.php'; // show user bar at the top
?>

<div style="max-width: 900px; margin: 0 auto; text-align: center;">
    <h2>Your Reservation History</h2>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">
            <tr style="background-color: #ddd;">
                <th>City</th>
                <th>Room Type</th>
                <th>Rooms</th>
                <th>Guests</th>
                <th>Nights</th>
                <th>Price</th>
                <th>Check-in</th>
                <th>Check-out</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['city']); ?></td>
                    <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                    <td><?php echo (int)$row['num_rooms']; ?></td>
                    <td><?php echo (int)$row['guests']; ?></td>
                    <td><?php echo (int)$row['nights']; ?></td>
                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['CHECKIN_DATETIME']); ?></td>
                    <td><?php echo htmlspecialchars($row['CHECKOUT_DATETIME']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No reservations found.</p>
    <?php endif; ?>

    <button onclick="history.back()" style="margin-top: 20px; padding: 8px 16px; font-size: 1rem; cursor: pointer;">Back</button>
</div>

<?php
$stmt->close();
$conn->close();
?>
