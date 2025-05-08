<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure debugging lines are commented out or removed
    // echo "<pre>";
    // print_r($_POST); // Debugging: Print the entire $_POST array
    // echo "</pre>";

    echo "Name: " . htmlspecialchars($_POST['fname']) . "<br>";
    echo "Email: " . htmlspecialchars($_POST['email']) . "<br>";
    echo "Password: " . htmlspecialchars($_POST['password']) . "<br>"; // Avoid showing passwords in production
    echo "Confirm Password: " . htmlspecialchars($_POST['cpassword']) . "<br>";
    echo "Gender: " . htmlspecialchars($_POST['gender']) . "<br>";
    echo "Date of Birth: " . htmlspecialchars($_POST['dob']) . "<br>";
    echo "Country: " . htmlspecialchars($_POST['Country']) . "<br>";
    echo "Terms: " . (isset($_POST['terms']) ? "Agreed" : "Not Agreed") . "<br>";
} else {
    echo "Invalid Request";
}
?>
