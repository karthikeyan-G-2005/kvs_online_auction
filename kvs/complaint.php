<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// MySQL Connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "kvs_auction";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$success = "";
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $order_id = trim($_POST["order_id"]);
    $complaint = trim($_POST["complaint"]);

    if (!empty($name) && !empty($email) && !empty($complaint)) {
        $stmt = $conn->prepare("INSERT INTO complaints (name, email, order_id, complaint) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $order_id, $complaint);
        if ($stmt->execute()) {
            $success = "Complaint submitted successfully!";
        } else {
            $error = "Something went wrong. Please try again.";
        }
        $stmt->close();
    } else {
        $error = "Please fill in all required fields.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Complaint Registration - KVS Online Auction</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #333;
            padding: 15px;
            color: white;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 8px 12px;
            border-radius: 5px;
        }

        .nav-links a:hover {
            background: #555;
        }

        .auth-icons {
            display: flex;
            gap: 15px;
        }

        .auth-icons span {
            color: white;
        }

        .header {
            background: #d9534f;
            color: white;
            padding: 15px;
            font-size: 22px;
            text-align: center;
            position: relative;
        }

        .user-email {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 16px;
            color: #ffeb3b;
        }

        .container {
            width: 40%;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        h2 {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
            text-align: left;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            background: #d9534f;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }

        button:hover {
            background: #c9302c;
        }

        .footer {
            margin-top: 40px;
            background: #333;
            color: white;
            padding: 20px;
            font-size: 16px;
            text-align: center;
        }

        .home-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 20px;
            text-decoration: none;
            display: inline-block;
        }

        .home-btn:hover {
            background: #0056b3;
        }

        .msg-success {
            color: green;
            margin-bottom: 15px;
        }

        .msg-error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="bidding.php">Bidding</a>
            <a href="contact.php">Contact</a>
            <a href="complaint.php">Complaint</a>
            <a href="feedback.php">Feedback</a>
        </div>
        <div class="auth-icons">
            <span>👋 Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" style="color:white; text-decoration:none;">Logout</a>
        </div>
    </div>

    <div class="header">
        Complaint Registration
        <div class="user-email">Logged in as: <?php echo htmlspecialchars($_SESSION['username']); ?></div>
    </div>

    <div class="container">
        <h2>Register a Complaint</h2>

        <?php if (!empty($success)) echo "<div class='msg-success'>$success</div>"; ?>
        <?php if (!empty($error)) echo "<div class='msg-error'>$error</div>"; ?>

        <form method="POST" action="">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="order_id">Order ID</label>
            <input type="text" id="order_id" name="order_id">

            <label for="complaint">Complaint Details</label>
            <textarea id="complaint" name="complaint" rows="4" required></textarea>

            <button type="submit">Submit Complaint</button>
        </form>

        <a href="home.php" class="home-btn">Go Home</a>
    </div>

    <div class="footer">
        &copy; 2025 KVS Online Auction System. All Rights Reserved.
    </div>

</body>
</html>
