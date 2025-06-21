<?php
include("connection.php");
session_start();

$msg = '';

if (isset($_POST['submit'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['name'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];

        if ($user['user_type'] === 'admin') {
            header('Location: admin.php');
        } else {
            header('Location: user.php');
        }
        exit();
    } else {
        $msg = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="form-container">
        <form action="" method="post">
            <h2>Login</h2>

            <?php if ($msg != ''): ?>
                <div class="msg"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>

            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Password" required />

            <button name="submit" type="submit">Login</button>

            <p class="forgot-password"><a href="forgot_password.php">Forgot Password?</a></p>

            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </form>
    </div>
</body>
</html>
