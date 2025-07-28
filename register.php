<?php
include 'db.php';

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $exists = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($exists->num_rows > 0) {
        $error = "Username already exists.";
    } else {
        $conn->query("INSERT INTO users (username, password) VALUES ('$username', '$password')");
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>Register</h2>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
    </div>
    <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
    </div>
    <button type="submit" class="btn btn-success">Register</button>
</form>

<p class="mt-3">Already a user? <a href="login.php">Login</a>.</p>

</body>
</html>
