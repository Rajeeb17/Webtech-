<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['location'])) {
    $_SESSION['selected_location'] = $_POST['location'];
    header("Location: select_room.php");
    exit();
} else {
    header("Location: select_location.php");
    exit();
}
?>