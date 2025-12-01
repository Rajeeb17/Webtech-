<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include 'user_bar.php';

// DB connection
$conn = new mysqli("localhost", "root", "", "hotel_reservation");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If POST request, save to session then redirect to avoid resubmission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['reservation'] = [
        'room_qty'   => $_POST['room_qty'] ?? [],
        'room_price' => $_POST['room_price'] ?? [],
        'guests'     => $_POST['guests'] ?? 1,
        'nights'     => $_POST['nights'] ?? 1,
        'location'   => $_SESSION['selected_location'] ?? 0,
        'checkin'    => $_POST['checkin'] ?? '',
        'checkout'   => $_POST['checkout'] ?? ''
    ];
    header("Location: confirm_room.php");
    exit();
}

$res = $_SESSION['reservation'] ?? null;

if (!$res) {
    echo "<p>No reservation data found. Please select a room first.</p>";
    exit();
}

// Validation
$checkin = new DateTime($res['checkin'] ?? '');
$checkout = new DateTime($res['checkout'] ?? '');
$now = new DateTime();

if ($checkin < $now) {
    echo "<p style='color:red; text-align:center;'>Error: Check-in cannot be in the past.</p>";
    exit();
}
if ($checkout <= $checkin) {
    echo "<p style='color:red; text-align:center;'>Error: Check-out must be after check-in.</p>";
    exit();
}
$total_rooms = 0;
foreach ($res['room_qty'] as $qty) {
    $total_rooms += (int)$qty;
}
if ($total_rooms <= 0) {
    echo "<p style='color:red; text-align:center;'>Error: You must book at least one room.</p>";
    exit();
}

// Fetch city name from DB
$location_id = (int)$res['location'];
$city = 'Unknown';
$sql = "SELECT city FROM location WHERE id = $location_id LIMIT 1";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $city = $row['city'];
}
$conn->close();

// Duration
$interval = $checkin->diff($checkout);
$duration = $interval->days . ' night(s), ' . $interval->h . ' hour(s)';
$total_hours = ($interval->days * 24) + $interval->h + ($interval->i > 0 ? 1 : 0);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Review Reservation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .review-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            margin: 40px auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .review-container h2 { text-align: center; }
        .review-list { list-style: none; padding: 0; }
        .review-list li { margin-bottom: 10px; }
        .review-actions { text-align: center; margin-top: 20px; }
        .review-actions button { margin: 0 10px; padding: 8px 24px; }

        /* Confirm button style */
        button[type="submit"][name="confirm"] {
            background-color: #2b6cb0;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            padding: 8px 24px;
            transition: background-color 0.3s ease;
        }
        button[type="submit"][name="confirm"]:hover {
            background-color: #2c5282;
        }
        /* Cancel button style */
        button[type="submit"][name="cancel"] {
            background-color: #e53e3e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            padding: 8px 24px;
            transition: background-color 0.3s ease;
        }
        button[type="submit"][name="cancel"]:hover {
            background-color: #9b2c2c;
        }

        .room-list-inner { margin-left: 20px; }
    </style>
</head>
<body>
    <div class="review-container">
        <h2>Review Your Reservation</h2>
        <ul class="review-list">
            <li><strong>Location city:</strong> <?php echo htmlspecialchars($city); ?></li>
            <li><strong>Rooms:</strong>
                <ul class="room-list-inner">
                    <?php
                    $total = 0;
                    foreach ($res['room_qty'] as $type => $qty):
                        if ($qty > 0):
                            $price = $res['room_price'][$type] ?? 0;
                            $price_per_hour = $price / 24;
                            $subtotal = $qty * $price_per_hour * $total_hours;
                            $total += $subtotal;
                    ?>
                        <li>
                            <?php echo htmlspecialchars($type); ?>: <?php echo (int)$qty; ?> x ৳<?php echo number_format($price); ?> x <?php echo $total_hours; ?> hour(s)
                            = <strong>৳<?php echo number_format($subtotal); ?></strong>
                        </li>
                    <?php endif; endforeach; ?>
                </ul>
            </li>
            <li><strong>Number of Guests:</strong> <?php echo (int)$res['guests']; ?></li>
            <li><strong>Total Price:</strong> <span style="color:#e53e3e;">৳<?php echo number_format($total); ?></span></li>
            <li><strong>Check-in:</strong> <?php echo htmlspecialchars($res['checkin']); ?></li>
            <li><strong>Check-out:</strong> <?php echo htmlspecialchars($res['checkout']); ?></li>
            <li><strong>Duration of Stay:</strong> <?php echo $duration; ?></li>
        </ul>
        <form method="post" action="finalize_reservation.php" class="review-actions">
            <button type="submit" name="confirm" value="1">Confirm</button>
            <button type="submit" name="cancel" value="1">Cancel</button>
        </form>
    </div>
</body>
</html>
