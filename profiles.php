<?php
session_start();

// Include database connection
require "config/connect.php";

if (isset($_SESSION["user_id"])) {
    $id = $_SESSION["user_id"];

    // Fetch user data including profile picture
    $sql = "SELECT * FROM user LEFT JOIN photos ON user.USER_ID = photos.USER_ID WHERE user.USER_ID = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Handle form submission for profile details
    if (isset($_POST["submit_details"])) {
        $name = $_POST["name"] ?? "";
        $phone = $_POST["phone"] ?? "";
        $residence = $_POST["residence"] ?? "";

        // Update profile information
        $updateSql = "UPDATE user SET USERNAME=?, PHONE=?, RESIDENCE=? WHERE USER_ID = ?";
        $stmt = $mysqli->prepare($updateSql);
        $stmt->bind_param("sssi", $name, $phone, $residence, $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Redirect after successful update
            header("Location: auth/redirect/save-changes.html");
            exit;
        } else {
            // Redirect if update failed
            header("Location: auth/redirect/update-failed.html");
            exit;
        }
    }

// Handle profile picture submission
if (isset($_POST["submit_picture"])) {
    // Check if a new profile picture is uploaded
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES["profile_photo"]["tmp_name"];
        $filename = $_FILES["profile_photo"]["name"];
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $new_filename = uniqid() . "." . $extension;
        $upload_directory = "/media/profile_pictures/";
        $destination = $_SERVER['DOCUMENT_ROOT'] . $upload_directory . $new_filename;

        if (move_uploaded_file($tmp_name, $destination)) {
            // Save picture URL in the database
            $photo_url = $upload_directory . $new_filename;

            // Update the photo URL in the photos table if it already exists for the user
            $updatePhotoSql = "UPDATE photos SET PHOTO_URL=? WHERE USER_ID=?";
            $stmt = $mysqli->prepare($updatePhotoSql);
            $stmt->bind_param("si", $photo_url, $id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                // Redirect after successful update
                header("Location: auth/redirect/save-changes.html");
                exit;
            } else {
                // Handle if no existing entry found, insert new entry
                $insertPhotoSql = "INSERT INTO photos (USER_ID, PHOTO_URL) VALUES (?, ?)";
                $stmt = $mysqli->prepare($insertPhotoSql);
                $stmt->bind_param("is", $id, $photo_url);
                $stmt->execute();

                // Redirect after successful insert
                header("Location: auth/redirect/save-changes.html");
                exit;
            }
        } else {
            // Redirect if upload failed
            header("Location: auth/redirect/update-failed.html");
            exit;
        }
    } else {
        // Redirect if upload failed
        header("Location: auth/redirect/update-failed.html");
        exit;
    }
}

} else {
    // Redirect if user is not authenticated
    header("Location: ../auth/login.php");
    exit;
}


// Define the URL for the back button based on the user's role
switch ($user['ROLE']) {
    case 'customer':
        $backUrl = "users/customer.php";
        break;
    case 'janitor':
        $backUrl = "users/janitor.php";
        break;
    case 'plumber':
        $backUrl = "users/plumber.php";
        break;
    case 'electrician':
        $backUrl = "users/electrician.php";
        break;
    case 'carpenter':
        $backUrl = "users/carpenter.php";
        break;
    default:
        // Default back URL if role is unknown
        $backUrl = "index.php";
        break;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="/css/styles.css">
    <script>
        function selectProfilePicture() {
            document.getElementById('profile-photo').addEventListener('change', function() {
                var file = this.files[0];
                    if (file) {
                        var reader = new FileReader();
                        reader.onload = function(event) {
                            document.getElementById('profile-picture').src = event.target.result;
                        }
                    reader.readAsDataURL(file);
                    }
            });
            document.getElementById('profile-photo').click();
        }
    </script>
</head>
<body>
    <div class="form-container" id="register-form">
        <form action="profiles.php" method="post" enctype="multipart/form-data">
            <h2>Update Profile</h2>

            <div class="profile-picture" onclick="selectProfilePicture()">
                <input type="file" id="profile-photo" name="profile_photo" style="display: none;" accept="image/*">
                <img src="<?php echo isset($user['PHOTO_URL']) ? $user['PHOTO_URL'] : '/media/profile.jpg'; ?>" alt="Profile Picture" id="profile-picture">
            </div>
            <div class="form-group">
                <button type="submit" name="submit_picture">Update Picture</button>
            </div>

            <div class="form-group">
                <label for="name">Username:</label>
                <input type="text" id="name" name="name" value="<?php echo $user['USERNAME']; ?>">
            </div>

            <div class="form-group">
                <label for="email">Area of Residence:</label>
                <input type="email" id="email" name="email" value="<?php echo $user['EMAIL']; ?>">
            </div>
        
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" value="<?php echo $user['PHONE']; ?>">
            </div>

            <div class="form-group">
                <label for="residence">Area of Residence:</label>
                <input type="text" id="residence" name="residence" value="<?php echo $user['RESIDENCE']; ?>">
            </div>

            <div class="form-group">
                <label for="role">User Role:</label>
                <input type="text" id="role" name="role" value="<?php echo $user['ROLE']; ?>" readonly>
            </div>
            <div class="form-group">
                <button type="submit" name="submit_details">Update Details</button>
                <a href="<?php echo $backUrl; ?>"><button type="button">Back to Home</button></a>
            </div>
        </form>
    </div>
</body>
</html>
