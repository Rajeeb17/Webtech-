<?php
session_start();
include 'user_bar.php';

if (!isset($_SESSION['selected_location'])) {
    header("Location: select_location.php");
    exit();
}

$rooms = [
    [
        'type' => 'Suite',
        'desc' => 'A luxurious suite with king-size bed, living area, and balcony.',
        'img'  => 'suite.jpg',
        'price' => 5000
    ],
    [
        'type' => 'Double',
        'desc' => 'A comfortable double bed for two guests.',
        'img'  => 'double.jpg',
        'price' => 3000
    ],
    [
        'type' => 'Single',
        'desc' => 'A cozy single room for solo travelers.',
        'img'  => 'single.jpg',
        'price' => 2000
    ]
];
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="icon" href="windhotel.png" type="image/png">

    <title>Select Room Type</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }
        .main-box {
            max-width: 850px;
            margin: 0 auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            padding: 40px 32px 32px 32px;
        }
        .main-box h2 {
            margin-top: 0;
            font-size: 2rem;
            margin-bottom: 24px;
        }
        .room-list {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .room-card {
            border: 1px solid #cbd5e0;
            border-radius: 10px;
            padding: 20px;
            width: 220px;
            text-align: center;
            background: #f9f9fb;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .room-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
        }
        .room-card h3 { margin: 10px 0 5px 0; }
        label {
            font-weight: 500;
            display: block;
            margin-bottom: 6px;
        }
        .room-card input[type="number"],
        .main-input {
            width: 100%;
            padding: 8px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            margin-bottom: 10px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        input[type="submit"] {
            padding: 10px 30px;
            background: #667eea;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.1rem;
            margin-top: 20px;
            transition: background 0.2s;
        }
        input[type="submit"]:hover {
            background: #5a67d8;
        }
    </style>
</head>
<body>

<div class="main-box">
    <h2>Select Room Type</h2>
    <form method="post" action="confirm_room.php" id="roomForm">
        <div class="room-list">
            <?php foreach ($rooms as $room): ?>
            <div class="room-card">
                <img src="images/<?php echo htmlspecialchars($room['img']); ?>" alt="<?php echo htmlspecialchars($room['type']); ?>">
                <h3><?php echo htmlspecialchars($room['type']); ?></h3>
                <p><?php echo htmlspecialchars($room['desc']); ?></p>
                <p><strong>Price per night:</strong> ৳<?php echo number_format($room['price']); ?></p>
                <label>Number of <?php echo htmlspecialchars($room['type']); ?> rooms:</label>
                <input 
                    type="number" 
                    name="room_qty[<?php echo htmlspecialchars($room['type']); ?>]" 
                    min="0" max="10" value="0" 
                    required
                >
                <input 
                    type="hidden" 
                    name="room_price[<?php echo htmlspecialchars($room['type']); ?>]" 
                    value="<?php echo $room['price']; ?>"
                >
            </div>
            <?php endforeach; ?>
        </div>

        <div style="display: flex; gap: 20px;">
            <div style="flex: 1;">
                <label>Number of Guests:</label>
                <input type="number" class="main-input" name="guests" min="1" max="10" required>
            </div>
        </div>

        <div style="display: flex; gap: 20px; margin-top: 16px;">
            <div style="flex: 1;">
                <label>Check-in Date and Time:</label>
                <input type="datetime-local" class="main-input" name="checkin" id="checkin" required>
            </div>
            <div style="flex: 1;">
                <label>Check-out Date and Time:</label>
                <input type="datetime-local" class="main-input" name="checkout" id="checkout" required>
            </div>
            <div style="flex: 1;">
                <label>Duration of Stay (nights):</label>
                <input type="text" class="main-input" name="nights" id="nights" readonly required style="margin-bottom: 0;">
            </div>
        </div>

        <div style="text-align: center;">
            <input type="submit" value="Submit">
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function calculateNights() {
        const checkin = new Date(document.getElementById('checkin').value);
        const checkout = new Date(document.getElementById('checkout').value);
        const nightsInput = document.getElementById('nights');

        if (document.getElementById('checkin').value && document.getElementById('checkout').value) {
            if (checkout <= checkin) {
                nightsInput.value = '';
                return;
            }
            const diff = checkout - checkin;
            const nights = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            nightsInput.value = `${nights} night(s), ${hours} hour(s), ${minutes} minute(s)`;
        } else {
            nightsInput.value = '';
        }
    }

    document.getElementById('checkin').addEventListener('change', calculateNights);
    document.getElementById('checkout').addEventListener('change', calculateNights);

    document.getElementById('roomForm').addEventListener('submit', function(e) {
        const checkin = new Date(document.getElementById('checkin').value);
        const checkout = new Date(document.getElementById('checkout').value);
        const now = new Date();

        // Prevent submission if check-in is in the past
        if (checkin < now) {
            e.preventDefault();
            alert("Check-in time cannot be in the past.");
            return;
        }

        // Prevent submission if check-out is not after check-in
        if (checkout <= checkin) {
            e.preventDefault();
            alert("Check-out time must be after check-in time.");
            return;
        }

        // At least one room must be selected
        const roomInputs = document.querySelectorAll('input[name^="room_qty"]');
        let totalRooms = 0;

        roomInputs.forEach(input => {
            const val = parseInt(input.value, 10);
            if (!isNaN(val)) totalRooms += val;
        });

        if (totalRooms <= 0) {
            e.preventDefault();
            alert('Please select at least one room.');
        }
    });
});
</script>

</body>
</html>
