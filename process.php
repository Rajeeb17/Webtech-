
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<h2>Registration Data</h2>";
    echo "Full Name: " . htmlspecialchars($_POST["fname"]) . "<br>";
    echo "Email: " . htmlspecialchars($_POST["email"]) . "<br>";
    echo "Gender: " . htmlspecialchars($_POST["gender"]) . "<br>";
    echo "DOB: " . htmlspecialchars($_POST["dob"]) . "<br>";
    echo "Country: " . htmlspecialchars($_POST["Country"]) . "<br>";
    echo "Opinion: " . htmlspecialchars($_POST["opinion"]) . "<br>";
    echo "Background Color: " . htmlspecialchars($_POST["bgcolor"]) . "<br>";
} else {
    echo "No data submitted.";
}
?>