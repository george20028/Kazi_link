<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

// Include database connection
$mysqli = require "config/connect.php";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accept_request'])) {
    $request_id = $_POST['request_id'];

    // Fetch service provider's role
    $user_query = "SELECT ROLE, USER_ID FROM user WHERE user_id = ?";
    $stmt = $mysqli->prepare($user_query);
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows > 0) {
        $user_row = $user_result->fetch_assoc();
        $service_provider_id = $user_row['USER_ID'];
        $service_provider_role = $user_row['ROLE'];

        // Fetch requested service's role
        $request_query = "SELECT PROFESSIONAL_ROLE FROM service_requests WHERE REQUEST_ID = ?";
        $stmt = $mysqli->prepare($request_query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $request_result = $stmt->get_result();

        if ($request_result->num_rows > 0) {
            $request_row = $request_result->fetch_assoc();
            $requested_role = $request_row['PROFESSIONAL_ROLE'];

            // Check if the service provider's role matches the requested role
            if ($service_provider_role === $requested_role) {
                // Update request status
                $update_query = "UPDATE service_requests SET REQUEST_STATUS = 'taken', SERVICE_PROVIDER_ID = ? WHERE REQUEST_ID = ?";
                $stmt = $mysqli->prepare($update_query);
                $stmt->bind_param("ii", $service_provider_id, $request_id);

                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    // Redirect if the request was successfully accepted
                    header("Location: auth/redirect/reqAccepted.html");
                    exit();
                }
            } else {
                // Redirect if the request was successfully accepted
                header("Location: auth/redirect/reqDenied.html");
                exit();
            }
        } else {
            echo '<p>Request not found</p>';
        }
    } else {
        echo '<p>User not found</p>';
    }
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
<style>
p{
    color: black;
}


</style>

</head>
<body>
<div class="form-container" id="services-form">
    <form action="viewrequests.php" method="GET">
        <h2>Requested Services</h2>
        <div class="form-group-search">
            <input type="text" name="search" class="search-bar" placeholder="Search by location..." value="<?php if (isset($_GET['search'])) { echo $_GET['search']; } ?>">
            <button type="submit" class="search-button">Search</button>
        </div>
        <div class="form-group">
            <a href="services.php"><button type="button">Back to Services</button></a>
        </div>

        <!-- Radio buttons for filtering service requests -->
        <label><input type="radio" name="filter" value="all" onchange="this.form.submit()" <?php if ((!isset($_GET['filter'])) || $_GET['filter'] == 'all') { echo 'checked'; } ?>>All</label>
        <label><input type="radio" name="filter" value="plumber" onchange="this.form.submit()" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'plumber') { echo 'checked'; } ?>>Plumber</label>
        <label><input type="radio" name="filter" value="carpenter" onchange="this.form.submit()" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'carpenter') { echo 'checked'; } ?>>Carpenter</label>
        <label><input type="radio" name="filter" value="janitor" onchange="this.form.submit()" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'janitor') { echo 'checked'; } ?>>Janitor</label>
        <label><input type="radio" name="filter" value="electrician" onchange="this.form.submit()" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'electrician') { echo 'checked'; } ?>>Electrician</label>
    </form>
        <div class="news-container">
            <?php
            $query = "SELECT * FROM service_requests ";

            if (isset($_GET['search'])) {
                $searchValue = $_GET['search'];
                // Modify the query based on the selected filter
                $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

                if (!empty($searchValue)) {
                    $query .= " WHERE CUSTOMER_RESIDENCE LIKE '%$searchValue%'";
                    if ($filter !== 'all') {
                        $query .= " AND PROFESSIONAL_ROLE = '$filter'";
                    }
                } elseif ($filter !== 'all') {
                    $query .= " WHERE PROFESSIONAL_ROLE = '$filter'";
                }
            }

            $query_run = $mysqli->query($query);
            if ($query_run) {
                if ($query_run->num_rows > 0) {
                    while ($row = $query_run->fetch_assoc()) {
                        ?>
                        <div class="news-card">
                            <div class="news-content">
                                <p style="text-align: left;font-weight: 500px;">Client's Name: <strong><?= $row['CUSTOMER_NAME']; ?></strong></p>
                                <p style="text-align: left;font-weight: 10px;">Client's Contact: <?= $row['CUSTOMER_CONTACT']; ?></p>
                                <p style="text-align: left;font-weight: 10px;">Professional Needed: <?= $row['PROFESSIONAL_ROLE']; ?></p>
                                <p style="text-align: left;font-weight: 10px;">Service Required: <?= $row['SERVICE']; ?></p>
                                <p style="text-align: left;font-weight: 5px;">Client's Residence: <small><?= $row['CUSTOMER_RESIDENCE']; ?></small></p><br>
                                <p style="text-align: left; font-weight: 500;">
                                    Status: 
                                    <strong style="color: <?= ($row['REQUEST_STATUS'] == 'open') ? 'green' : 'red'; ?>;">
                                        <?= $row['REQUEST_STATUS']; ?>
                                    </strong>
                                </p>
                                <?php if ($row['REQUEST_STATUS'] == 'open' && $row['USER_ID'] != $_SESSION["user_id"]) { ?>
                                    <form action="viewrequests.php" method="POST">
                                        <div class="form-group">
                                            <input type="hidden" name="request_id" value="<?= $row['REQUEST_ID']; ?>">
                                            <button type="submit" name="accept_request">Accept Request</button>
                                            <button type="button" class="contact_client" data-contact="<?= $row['CUSTOMER_CONTACT']; ?>">Contact Client</button>
                                        </div>
                                    </form>
                                <?php } elseif ($row['REQUEST_STATUS'] == 'taken' && $_SESSION["user_id"] == $row['SERVICE_PROVIDER_ID']) { ?>
                                    <form action="viewrequests.php" method="POST">
                                        <div class="form-group">
                                            <input type="hidden" name="request_id" value="<?= $row['REQUEST_ID']; ?>">
                                            <button type="button" class="contact_client" data-contact="<?= $row['CUSTOMER_CONTACT']; ?>">Contact Client</button>
                                        </div>
                                    </form>
                                <?php } ?>

                            <script>
                                document.querySelectorAll(".contact_client").forEach(function(button) {
                                    button.addEventListener("click", function() {
                                        // Fetch client's contact from the data attribute
                                        var clientContact = this.getAttribute("data-contact");
            
                                        // Redirect to the application responsible for making calls
                                        window.location.href = "tel:" + clientContact;
                                    });
                                });
                            </script>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>No Record Found</p>';
                }
            } else {
                echo '<p>Error retrieving data</p>';
            }
            ?>
        </div>
</div>
</body>
</html>
