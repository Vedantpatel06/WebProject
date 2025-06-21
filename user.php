<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'user') {
    header('Location: login.php');
    exit();
}

include("connection.php");

$user_id = $_SESSION['user_id'];

// Fetch user data
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$msg = '';

// Handle update
if (isset($_POST['update'])) {
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $user_type   = mysqli_real_escape_string($conn, $_POST['user_type']);
    $address     = mysqli_real_escape_string($conn, $_POST['address']);
    $dob         = $_POST['dob'];
    $mobile      = mysqli_real_escape_string($conn, $_POST['mobile']);
    $aadhar_no   = mysqli_real_escape_string($conn, $_POST['aadhar_no']);
    $education   = mysqli_real_escape_string($conn, $_POST['education']);
    $gender      = mysqli_real_escape_string($conn, $_POST['gender']);

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['name'] != '') {
        $target_dir = "uploads/";
        $profile_picture = basename($_FILES['profile_picture']['name']);
        $target_file = $target_dir . $profile_picture;
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file);
    } else {
        $target_file = $user['profile_picture'];
    }

    $update_query = "UPDATE users SET 
                        name = '$name', 
                        email = '$email', 
                        user_type = '$user_type', 
                        address = '$address', 
                        dob = '$dob', 
                        phone_number = '$mobile', 
                        aadhar_no = '$aadhar_no', 
                        education = '$education', 
                        gender = '$gender', 
                        profile_picture = '$target_file' 
                    WHERE id = '$user_id'";

    if (mysqli_query($conn, $update_query)) {
        $msg = "Profile updated successfully!";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);
    } else {
        $msg = "Error updating profile: " . mysqli_error($conn);
    }
}

// Handle delete
if (isset($_POST['delete'])) {
    $delete_query = "DELETE FROM users WHERE id = '$user_id'";
    if (mysqli_query($conn, $delete_query)) {
        session_destroy();
        header('Location: login.php');
        exit();
    } else {
        $msg = "Error deleting account: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Profile</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="form-container">
        <h2>Your Profile, <?= htmlspecialchars($user['name']) ?></h2>

        <?php if($msg != ''): ?>
            <div class="msg"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture" />

        <form action="" method="post" enctype="multipart/form-data">
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" placeholder="Name" required />
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Email" required />
            <select name="user_type" required>
                <option value="user" <?= $user['user_type'] === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['user_type'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male" <?= $user['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $user['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                <option value="Other" <?= $user['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
            </select>
            <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" placeholder="Address" required />
            <input type="date" name="dob" value="<?= htmlspecialchars($user['dob']) ?>" placeholder="Date of Birth" required />
            <input type="text" name="mobile" value="<?= htmlspecialchars($user['phone_number']) ?>" placeholder="Mobile Number" maxlength="10" required />
            <input type="text" name="aadhar_no" value="<?= htmlspecialchars($user['aadhar_no']) ?>" placeholder="Aadhar Number" maxlength="12" required />
            <input type="text" name="education" value="<?= htmlspecialchars($user['education']) ?>" placeholder="Education" required />
            <input type="file" name="profile_picture" />

            <button type="submit" name="update">Update Profile</button>
            <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">Delete Account</button>
        </form>

        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
