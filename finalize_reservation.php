<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include 'user_bar.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Connect to DB
$conn = new mysqli("localhost", "root", "", "hotel_reservation");
if ($conn->connect_error) {
    die("DB error: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel'])) {
        unset($_SESSION['reservation']);
        header("Location: select_room.php");
        exit();
    }

    if (isset($_POST['confirm']) && isset($_SESSION['reservation'])) {
        $res = $_SESSION['reservation'];
        $username = $_SESSION['username'];

        // Get city from location ID
        $city = '';
        $stmt_city = $conn->prepare("SELECT city FROM location WHERE id = ?");
        $stmt_city->bind_param("i", $res['location']);
        $stmt_city->execute();
        $stmt_city->bind_result($city);
        $stmt_city->fetch();
        $stmt_city->close();

        $checkin = $res['checkin'];
        $checkout = $res['checkout'];

        $stmt = $conn->prepare("INSERT INTO hotel_reservinfo (
            location_id, city, room_type, num_rooms, guests, nights, username, price, CHECKIN_DATETIME, CHECKOUT_DATETIME
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $print_summary = [];

        foreach ($res['room_qty'] as $room_type => $num_rooms) {
            if ($num_rooms > 0) {
                $price_per_night = $res['room_price'][$room_type] ?? 0;

                $in  = new DateTime($checkin);
                $out = new DateTime($checkout);
                $interval = $in->diff($out);
                $nights = $interval->days;

                $total_hours = ($interval->days * 24) + $interval->h + ($interval->i > 0 ? 1 : 0);
                $price_per_hour = $price_per_night / 24;
                $subtotal = $num_rooms * $price_per_hour * $total_hours;

                $stmt->bind_param(
                    "issiiisdss",
                    $res['location'],
                    $city,
                    $room_type,
                    $num_rooms,
                    $res['guests'],
                    $nights,
                    $username,
                    $subtotal,
                    $checkin,
                    $checkout
                );

                if (!$stmt->execute()) {
                    die("Insert failed: " . $stmt->error);
                }

                $print_summary[] = [
                    'room_type' => $room_type,
                    'num_rooms' => $num_rooms,
                    'guests' => $res['guests'],
                    'nights' => $nights,
                    'checkin' => $checkin,
                    'checkout' => $checkout,
                    'subtotal' => $subtotal,
                    'city' => $city,
                ];
            }
        }

        $stmt->close();
        $conn->close();

        $_SESSION['reservation_success'] = true;
        $_SESSION['reservation_summary'] = $print_summary;
        header("Location: finalize_reservation.php");
        exit();
    }
}

// Show success page
if (isset($_SESSION['reservation_success']) && $_SESSION['reservation_success'] === true) {
    $summary = $_SESSION['reservation_summary'] ?? [];
    unset($_SESSION['reservation_success']);
    unset($_SESSION['reservation_summary']);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Reservation Successful</title>
        <style>
            body {
                font-family: 'Segoe UI', sans-serif;
                background-color: #f7f7f7;
                margin: 0;
                padding: 0;
            }
            .success-box {
                max-width: 800px;
                margin: 40px auto;
                background: #fff;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }
            h2 {
                color: #667eea;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                padding: 10px;
                border-bottom: 1px solid #ccc;
                text-align: left;
            }
            .buttons {
                margin-top: 30px;
                text-align: center;
            }
            .buttons a, .buttons button {
                display: inline-block;
                margin: 0 10px;
                padding: 10px 20px;
                background: #667eea;
                color: #fff;
                text-decoration: none;
                border: none;
                border-radius: 6px;
                font-weight: bold;
                cursor: pointer;
            }
            .buttons button.print {
                background-color: #4CAF50;
            }
            .buttons a:hover,
            .buttons button:hover {
                background-color: #5a67d8;
            }
        </style>
    </head>
    <body>
        <div class="success-box" id="print-area">
            <div style="text-align: center;">
                <h2>Reservation Confirmed!</h2>
                <p>Thank you, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! Your reservation has been saved successfully.</p>
                <h3>Reservation Summary</h3>
            </div>

            <?php if (!empty($summary)): ?>
                <table>
                    <tr>
                        <th>City</th>
                        <th>Room Type</th>
                        <th>Rooms</th>
                        <th>Guests</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Nights</th>
                        <th>Total Price</th>
                    </tr>
                    <?php foreach ($summary as $s): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['city']); ?></td>
                            <td><?php echo htmlspecialchars($s['room_type']); ?></td>
                            <td><?php echo $s['num_rooms']; ?></td>
                            <td><?php echo $s['guests']; ?></td>
                            <td><?php echo $s['checkin']; ?></td>
                            <td><?php echo $s['checkout']; ?></td>
                            <td><?php echo $s['nights']; ?></td>
                            <td>৳<?php echo number_format($s['subtotal'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>

            <div class="buttons">
                <a href="select_location.php">Book Another</a>
                <button class="print" onclick="window.print()">Print Confirmation</button>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <h5>For any issues, please contact here:</h5>
                <h5><strong>Email:</strong> <a href="mailto:windhotel@gmail.com">windhotel@gmail.com</a></h5>
                <h5><strong>Phone:</strong> +880 123 456 7890</h5>
                <h5><strong>Head office Address:</strong> 123 Wind Hotel Street, Dhaka, Bangladesh</h5>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

header("Location: select_room.php");
exit();
?>
