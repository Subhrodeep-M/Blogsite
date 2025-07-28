<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = 'User not found. Please <a href="register.php">register</a>.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>Login</h2>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <?= $error ?>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
    </div>
    <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>

<p class="mt-3">Don't have an account? <a href="register.php">Register here</a>.</p>

</body>
</html>
