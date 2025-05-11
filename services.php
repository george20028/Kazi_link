<?php
    session_start();
    // Assuming the user role is retrieved and stored in a session variable named $_SESSION["user_role"]
    $userRole = $_SESSION["user_role"] ?? '';

    if (isset($_SESSION["user_id"])) {
        $mysqli = require "config/connect.php";

        $sql = "SELECT u.*, p.PHOTO_URL
            FROM user u
            LEFT JOIN photos p ON u.USER_ID = p.USER_ID
            WHERE u.USER_ID = {$_SESSION["user_id"]}";

        $result = $mysqli->query($sql);

        $user = $result->fetch_assoc();
    } else {
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css"/>
    <link rel="stylesheet" type="text/css" href="../css/menu.css" />
    <style>
        .news-image {
            width: 60%;
            height: 100%;
        }
        .news-image div {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
<div class="form-container" id="services-form">
    <form action="services.php" method="GET">
        <h2>Services</h2>
        <div class="form-group-search">
            <input type="text" name="search" class="search-bar" placeholder="Search by location..." value="<?php if (isset($_GET['search'])) { echo $_GET['search']; } ?>">
            <button type="submit" class="search-button">Search</button>
        </div>
<div class="form-group">
    <?php if ($user['ROLE'] !== 'customer') { ?>
        <a href="viewrequests.php"><button type="button">View Requests</button></a>
        <a href="servicerequest.php"><button type="button">Request Service</button></a>
        <a href="<?php echo $backUrl; ?>"><button type="button">Back to Home</button></a>
    <?php } else { ?>
        <a href="servicerequest.php"><button type="button">Request Service</button></a>
        <a href="<?php echo $backUrl; ?>"><button type="button">Back to Home</button></a>
    <?php } ?>
</div>
        <!-- Radio buttons for filtering service providers -->
        <label><input type="radio" name="filter" value="plumber" onchange="this.form.submit()" <?php if ((!isset($_GET['filter'])) || ($userRole == 'plumber')) echo "checked"; ?>>Plumber</label>
        <label><input type="radio" name="filter" value="carpenter" onchange="this.form.submit()" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'carpenter') echo "checked"; ?>>Carpenter</label>
        <label><input type="radio" name="filter" value="janitor" onchange="this.form.submit()" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'janitor') echo "checked"; ?>>Janitor</label>
        <label><input type="radio" name="filter" value="electrician" onchange="this.form.submit()" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'electrician') echo "checked"; ?>>Electrician</label>

        <div class="news-container">
            <?php
            $query = "SELECT * FROM user WHERE ROLE != 'customer'";

            if (isset($_GET['search'])) {
                $searchValue = $_GET['search'];
                // Modify the query based on the selected filter
                $filter = isset($_GET['filter']) ? $_GET['filter'] : '';

                if (!empty($searchValue) && !empty($filter)) {
                    $query .= " AND RESIDENCE LIKE '%$searchValue%' AND ROLE = '$filter'";
                } elseif (!empty($searchValue)) {
                    $query .= " AND RESIDENCE LIKE '%$searchValue%'";
                } elseif (!empty($filter)) {
                    $query .= " AND ROLE = '$filter'";
                }
            }

            $query_run = $mysqli->query($query);
            if (mysqli_num_rows($query_run) > 0) {
                while ($user = mysqli_fetch_assoc($query_run)) {
                    $user_id = $user['USER_ID'];
                    $media_query = "SELECT PHOTO_URL FROM photos WHERE USER_ID = '$user_id'";
                    $media_query_run = $mysqli->query($media_query);
                    if ($media_query_run && mysqli_num_rows($media_query_run) > 0) {
                        $media_data = mysqli_fetch_assoc($media_query_run);
                        $image = $media_data['PHOTO_URL'] ?? '';
                    } else {
                        $image = '';
                    }
                    ?>
                    <div class="news-card">
                        <?php
                        if (!empty($image)) {
                            echo '<div class="news-image"><img src="' . $image . '" alt="News Image"></div>';
                        }
                        ?>
                        <div class="news-content">
                            <p style="text-align: left;font-weight: 500px;"><strong><?= $user['USERNAME']; ?></strong></p>
                            <p style="text-align: left;font-weight: 10px;"><?= $user['ROLE']; ?></p>
                            <p style="text-align: left;font-weight: 10px;"><?= $user['PHONE']; ?></p>
                            <p style="text-align: left;font-weight: 5px;"><small><?= $user['RESIDENCE']; ?></small></p><br>
                            <?php if ($user['USER_ID'] != $_SESSION["user_id"]) { ?>
                                <div class="form-group">
                                    <a href="ratings.php"><button type="button">Add Ratings</button></a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p>No Record Found</p>';
            }
            ?>
        </div>
    </form>
</div>
</body>
</html>
