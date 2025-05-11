<?php
session_start();
require_once 'config/connect.php';

class User {
    public $name;
}

class ServiceRequest {
    public $customer;
    public $professional;
    public $service;
    public $time;
}

class IkoKaziPlatform {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function request_service($customer_name, $customer_residence, $customer_contact, $professional_role, $service, $time) {
        $user_id = $_SESSION['user_id'];
        $request_status = 'open';

        $sql = "INSERT INTO service_requests (USER_ID, CUSTOMER_NAME, CUSTOMER_RESIDENCE, CUSTOMER_CONTACT, PROFESSIONAL_ROLE, SERVICE, REQUEST_STATUS, TIME) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isssssss", $user_id, $customer_name, $customer_residence, $customer_contact, $professional_role, $service, $request_status, $time);

        if ($stmt->execute()) {
            header("Location: auth/redirect/save-changes.html");
            exit(); // Make sure to exit after redirecting
        } else {
            header("Location: auth/redirect/update-failed.html");
            exit(); // Make sure to exit after redirecting
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $platform = new IkoKaziPlatform($mysqli);

    $customer_name = $_POST['customer_name'];
    $professional_role = $_POST['professional_role'];
    $customer_residence = $_POST['customer_residence'];
    $customer_contact = $_POST['customer_contact'];
    $service = $_POST['service'];
    $time = $_POST['time'];

    $platform->request_service($customer_name, $customer_residence, $customer_contact, $professional_role, $service, $time);
}

$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM user WHERE USER_ID = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Request Form</title>
    <link rel="stylesheet" href="/css/styles.css">

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</head>
<body>
<div class="form-container" id="register-form">
    <form action="servicerequest.php" method="POST">
        <h2>Service Request Form</h2>
        <div class="form-group">
            <label for="customer_name">Customer Name:</label>
            <input type="text" id="customer_name" name="customer_name" value="<?php echo $user['USERNAME']; ?>" readonly>
        </div>

        <div class="form-group">
            <label for="professional_role">Whose expertise do you seek?</label>
            <select id="professional_role" name="professional_role" required>
                <option value="plumber">Plumber</option>
                <option value="carpenter">Carpenter</option>
                <option value="electrician">Electrician</option>
                <option value="janitor">Janitor</option>
            </select>
        </div>

        <div class="form-group">
            <label for="service">Service Required:</label>
            <input type="text" id="service" name="service" required>
        </div>

        <div class="form-group">
            <label for="customer_residence">Customer's Residence:</label>
            <input type="text" id="customer_residence" name="customer_residence" value="<?php echo $user['RESIDENCE']; ?>" readonly>
        </div>

        <div class="form-group">
            <label for="customer_contact">Customer's Contact:</label>
            <input type="text" id="customer_contact" name="customer_contact" value="<?php echo $user['PHONE']; ?>" readonly>
        </div>

        <div class="form-group">
            <label for="time">Preferred time for the job to be done:</label>
            <input type="text" id="time" name="time" placeholder="E.g., Monday, 10:00 AM" required>
        </div>

        <div class="form-group">
            <button type="submit">Submit</button>
            <button type="reset">Clear</button>
            <!--<button onclick="goBack()">Back</button>-->
        </div>
    </form>
</div>
</body>
</html>
