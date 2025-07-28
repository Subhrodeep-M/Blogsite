<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $post = $conn->real_escape_string($_POST['post']);

    $conn->query("INSERT INTO posts (username, post) VALUES ('$username', '$post')");
}

$_SESSION['toast'] = "Post added successfully!";
header("Location: index.php");
exit();
