<?php
$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $mysqli = require "../config/connect.php";

    $email = $mysqli->real_escape_string($_POST["email"]);

    $sql = sprintf("SELECT * FROM user
                   WHERE EMAIL = '%s'",
                   $email);

    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();

    if ($user) {

        if (password_verify($_POST["password"], $user["password_hash"])) {

            session_start();

            session_regenerate_id();

            $_SESSION["user_id"] = $user["USER_ID"];

            // Check user's role and redirect accordingly
            switch ($user["ROLE"]) {
                case "customer":
                    header("Location: ../users/customer.php");
                    break;
                case "electrician":
                    header("Location: ../users/electrician.php");
                    break;
                case "plumber":
                    header("Location: ../users/plumber.php");
                    break;
                case "carpenter":
                    header("Location: ../users/carpenter.php");
                    break;
                case "janitor":
                    header("Location: ../users/janitor.php");
                    break;
                default:
                    header("Location: ../index.php"); // Redirect to a default page if role not recognized
                    break;
            }
            exit;
        }
    }

    $is_invalid = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration Form</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <div class="form-container" id="login-form">

        <form method="post">
        <h2>Login</h2>
            <?php if ($is_invalid): ?>
                <em style="color:red">Invalid login</em>
            <?php endif; ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="******" autocomplete="off" required><br>
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
            <div class="form-group">
                <p>Do not have an account?
                <a href='/auth/registration.html'>Register</a></p>
            </div>
        </form>
    </div>
</body>
</html>
