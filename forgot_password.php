<?php
session_start();
include("connection.php");

$msg = "";
$step = 1;  // Step 1: enter email, Step 2: enter OTP, Step 3: reset password

if (isset($_POST['submit_email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['reset_email'] = $email;
        $_SESSION['mobile'] = $user['phone_number'];

        // Generate OTP
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = time() + 300; // OTP valid for 5 mins

        // TODO: Send OTP to mobile via SMS API here
        // For demo, we just display OTP (remove in production)
        $msg = "OTP sent to your registered mobile number: " . htmlspecialchars($_SESSION['mobile']) . ". <br> OTP (demo): $otp";

        $step = 2;
    } else {
        $msg = "Email not found!";
    }
}

if (isset($_POST['submit_otp'])) {
    $user_otp = $_POST['otp'];
    if (isset($_SESSION['otp']) && time() < $_SESSION['otp_expiry']) {
        if ($user_otp == $_SESSION['otp']) {
            $msg = "OTP verified. Please enter new password.";
            $step = 3;
        } else {
            $msg = "Incorrect OTP.";
            $step = 2;
        }
    } else {
        $msg = "OTP expired. Please try again.";
        session_unset();
        $step = 1;
    }
}

if (isset($_POST['submit_new_password'])) {
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if ($password !== $cpassword) {
        $msg = "Passwords do not match!";
        $step = 3;
    } else {
        if (!isset($_SESSION['reset_email'])) {
            $msg = "Session expired. Start again.";
            $step = 1;
        } else {
            $email = $_SESSION['reset_email'];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
            if (mysqli_query($conn, $update)) {
                $msg = "Password reset successfully. <a href='login.php'>Login here</a>.";
                session_unset();
                $step = 4; // done
            } else {
                $msg = "Error updating password: " . mysqli_error($conn);
                $step = 3;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Forgot Password</title>
<link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="form-container">
    <h2>Forgot Password</h2>

    <?php if ($msg != ''): ?>
        <div class="msg"><?= $msg ?></div>
    <?php endif; ?>

    <?php if ($step == 1): ?>
    <form method="post">
        <input type="email" name="email" placeholder="Enter your registered email" required />
        <button type="submit" name="submit_email">Send OTP</button>
    </form>

    <?php elseif ($step == 2): ?>
    <form method="post">
        <input type="text" name="otp" placeholder="Enter OTP" maxlength="6" required />
        <button type="submit" name="submit_otp">Verify OTP</button>
    </form>

    <?php elseif ($step == 3): ?>
    <form method="post">
        <input type="password" name="password" placeholder="New Password" required />
        <input type="password" name="cpassword" placeholder="Confirm Password" required />
        <button type="submit" name="submit_new_password">Reset Password</button>
    </form>

    <?php else: ?>
        <p><a href="login.php">Back to Login</a></p>
    <?php endif; ?>

</div>
</body>
</html>
