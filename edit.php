<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    die("Access denied. Please <a href='login.php'>login</a>.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $post_id = (int)$_POST['post_id'];
    $username = $_SESSION['username'];
    $new_post = $conn->real_escape_string($_POST['post']);

    // Check if this post belongs to the logged-in user
    $check = $conn->prepare("SELECT * FROM posts WHERE id = ? AND username = ?");
    $check->bind_param("is", $post_id, $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        die("Unauthorized action.");
    }

    // Update the post
    $update = $conn->prepare("UPDATE posts SET post = ?, updated_at = NOW() WHERE id = ?");
    $update->bind_param("si", $new_post, $post_id);
    $update->execute();

$_SESSION['toast'] = "Post updated successfully!";
header("Location: index.php");
exit();
    
} else {
    die("Invalid request method.");
}
?>
