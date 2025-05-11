<?php
session_start();

if (isset($_SESSION["user_id"])) {
    $mysqli = require "../config/connect.php";

    // Fetch user data including profile picture
    $sql = "SELECT * FROM user LEFT JOIN photos ON user.USER_ID = photos.USER_ID WHERE user.USER_ID = {$_SESSION["user_id"]}";
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();

    // Fetch the profile photo URL
    $profilePhoto = isset($user["PHOTO_URL"]) ? $user["PHOTO_URL"] : '/media/profile.jpg';
} else {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="../css/menu.css" />
    <link rel="stylesheet" type="text/css" href="../css/index.css" />
    <script src="../js/menu.js"></script>
</head>
<body>
<!-- Displaying the header and the menu "icon"-->
<div class="header">
    <div class="menu-toggle" onclick="toggleMenu()">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>

    
    <h1 class="title">IkoKazi</h1>
    <div class="profile-icon">
        <!-- Display the profile picture -->
        <img src="<?php echo $profilePhoto; ?>" alt="Profile Picture" id="profile-picture">
    </div>
</div>

<!-- Displaying the menu contents -->
<div class="menu-container">
    <ul class="menu">
        <li><a href="../profiles.php?ref=../users/plumber.php">View Profile</a></li>
        <li><a href="../services.php">Services</a></li>
        <li><a href="../aboutus.html">About us</a></li>
        <li><a href="../auth/logout.php">Logout</a></li>
    </ul>
</div>

<div class="content">
    <?php if (isset($user)) : ?>
        <h2>Welcome to IkoKazi</h2>
        <p>Hello <?= htmlspecialchars($user["USERNAME"]) ?></p>
    <?php endif; ?>
    <img src="../media/bg1.jpg" alt="Description of the image">
    <div class="image-description">
        <p>
        Welcome to IkoKazi - the premier destination for connecting skilled trade professionals with customers! 
        Our platform offers a seamless and reliable solution for finding and hiring professionals in carpentry, plumbing, electrical work and janitorial services. 
        Whether you need a quick fix or a major renovation, our user-friendly platform makes it easy to connect with trusted professionals, schedule appointments, and get the job done efficiently. 
        Join our community today and experience the convenience of hassle-free service exchange!
        </p>
    </div>
</div>
</body>
</html>
