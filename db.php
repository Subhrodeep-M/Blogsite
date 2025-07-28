<?php
$host = "localhost";
$user = "root"; // or your username
$pass = ""; // or your password
$dbname = "blog";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
