<?php
include("connection.php");
session_start();

$msg = '';

if (isset($_POST['submit'])) {
    $name      = $_POST['name'];
    $email     = $_POST['email'];
    $password  = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $user_type = $_POST['user_type'];
    $address   = $_POST['address'];
    $dob       = $_POST['dob'];
    $gender    = $_POST['gender'];
    $phone_number = $_POST['phone_number'];
    $aadhar_no = $_POST['aadhar_no'];
    $education = $_POST['education'];
    $profile_picture = $_FILES['profile_picture']['name'];

    // Mobile number validation logic
    if (!preg_match("/^[6789]\d{9}$/", $phone_number)) {
        $msg = "Mobile number must start with 6, 7, 8, or 9 and must be 10 digits long.";
    } else if ($password !== $cpassword) {
        $msg = "Passwords do not match!";
    } else {
        $check_query = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $msg = "User already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            if ($profile_picture) {
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($profile_picture);
                move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file);
            } else {
                $target_file = 'uploads/default-avatar.png';
            }

            $insert = "INSERT INTO users (name, email, password, user_type, address, dob, gender, phone_number, aadhar_no, education, profile_picture) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert);
            mysqli_stmt_bind_param($stmt, "sssssssssss", $name, $email, $hashed_password, $user_type, $address, $dob, $gender, $phone_number, $aadhar_no, $education, $target_file);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: login.php');
                exit();
            } else {
                $msg = "Registration failed: " . mysqli_error($conn);
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
  <title>Register</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="form-container">
    <form action="" method="post" enctype="multipart/form-data">
      <h2>Create New Account</h2>
      
      <?php if ($msg != ''): ?>
        <div class="msg"><?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>
      
      <input type="text" name="name" placeholder="Full Name" required />
      <input type="email" name="email" placeholder="Email Address" required />
      <input type="password" name="password" placeholder="Password" required />
      <input type="password" name="cpassword" placeholder="Confirm Password" required />
      
      <select name="user_type" required>
        <option value="">Select Role</option>
        <option value="user">User</option>
        <option value="admin">Admin</option>
      </select>
      <select name="gender" required>
        <option value="">Select Gender</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="other">Other</option>
      </select>

      <input type="text" name="address" placeholder="Address" required />
      <input type="date" name="dob" placeholder="Date of Birth" required />
      
      <!-- Mobile number input with validation pattern -->
      <input type="text" name="phone_number" placeholder="Mobile Number (10 digits)" 
        pattern="^[6789]\d{9}$" title="Enter a valid mobile number starting with 6, 7, 8, or 9" required />
      
      <input type="text" name="aadhar_no" placeholder="Aadhar Number (12 digits)" 
        pattern="\d{12}" title="Enter a 12 digit Aadhar number" required />
      
      <input type="text" name="education" placeholder="Education" required />

      <input type="file" name="profile_picture" />

      <button type="submit" name="submit">Register</button>
      <p>Already have an account? <a href="login.php">Login here</a></p>
    </form>
  </div>
</body>
</html>
