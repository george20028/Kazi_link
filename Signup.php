<?php
if (empty($_POST["username"])) {
    header("Location: /auth/redirect/usrname.html");
    exit;
}

if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    header("Location: /auth/redirect/validemail.html");
    exit;
}

if (strlen($_POST["password"]) < 6) {
    header("Location: /auth/redirect/pwdlength.html");
    exit;
}

if (!preg_match("/[a-z]/i", $_POST["password"])) {
    header("Location: /auth/redirect/pwdxter.html");
    exit;
}

if (!preg_match("/[0-9]/", $_POST["password"])) {
    header("Location: /auth/redirect/pwdno.html");
    exit;
}

if ($_POST["password"] !== $_POST["conpassword"]) {
    header("Location: /auth/redirect/pwderror.html");
    exit;
}

$mysqli = require "../config/connect.php";

if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

$stmt = $mysqli->prepare("SELECT 1 FROM user WHERE EMAIL = ?");
$stmt->bind_param("s", $_POST["email"]);
$stmt->execute();

if ($stmt->fetch()) {
    $stmt->close();
    header("Location: /auth/redirect/email-conflict.html");
    exit;
}

$stmt->close();

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$stmt = $mysqli->prepare("INSERT INTO user(USERNAME, EMAIL,PHONE,ROLE, password_hash) VALUES (?,?,?, ?, ?)");

$stmt->bind_param("ssiss", $_POST["username"], $_POST["email"], $_POST["phone"], $_POST["role"], $password_hash);

if (!$stmt) {
    die("SQL error: " . $mysqli->error);
}

if ($stmt->execute()) {
    $stmt->close();
    header("Location: /auth/redirect/signup-success.html");
    exit;
} else {
    $stmt->close();
    header("Location: /auth/redirect/signup-failed.html");
    exit;
}
?>
