<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM posts WHERE id=$id");
}

$_SESSION['toast'] = "Post deleted successfully!";
header("Location: index.php");
exit();
