<?php
// Start the session
session_start();

// Database credentials
$servername = "localhost";
$username = "root"; // Your database username
$password = "Nischay@2004"; // Your database password
$dbname = "user_db"; // Your database name

// Create connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']); // Hash the password for security

    // SQL query to check if the user exists
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    // Check if the user exists
    if ($result->num_rows > 0) {
        // Store email in session
        $_SESSION['email'] = $email;

        // Redirect to home page after successful login
        header("Location: home.php");
        exit(); // Stop further execution after redirect
    } else {
        // Display error if credentials are incorrect
        echo "Invalid email or password.";
    }
}

// Close the database connection
$conn->close();
?>
