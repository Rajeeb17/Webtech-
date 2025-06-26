<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "hotel_reservation";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(['exists' => false, 'error' => 'DB connection error']));
}

$nid = isset($_POST['nid']) ? $_POST['nid'] : '';
$response = ['exists' => false];

if ($nid !== '') {
    $stmt = $conn->prepare("SELECT NID FROM customer WHERE NID = ?");
    $stmt->bind_param("s", $nid);
    $stmt->execute();
    $stmt->store_result();
    $response['exists'] = $stmt->num_rows > 0;
    $stmt->close();
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>