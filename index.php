<?php

    session_start();

        if (isset($_SESSION["user_id"])) {
            $mysqli = require "config/connect.php";

            $sql = "SELECT * FROM user
                    WHERE USER_ID = {$_SESSION["user_id"]}";
    
            $result = $mysqli->query($sql);
    
            $user = $result->fetch_assoc();

        /* // Fetch the profile photo of the user
        $profilePhoto = $user["PROFILE_PHOTO"];*/
        } else {
            header("Location: ../auth/login.php");
            exit;
        }

?>

