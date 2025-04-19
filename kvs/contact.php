<?php
session_start();
$loggedInUser = $_SESSION['email'] ?? null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    // Connect to DB
    $conn = new mysqli("localhost", "root", "", "kvs_auction");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $message);
    
    if ($stmt->execute()) {
        echo "<script>alert('Thank you for contacting us!'); window.location.href='home.php';</script>";
    } else {
        echo "<script>alert('Error saving message.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - KVS Online Auction</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0; padding: 0;
            background: #f4f4f4;
            display: flex; flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            background: #333; padding: 15px; color: white;
        }
        .nav-links {
            display: flex; gap: 20px;
        }
        .nav-links a {
            color: white; text-decoration: none; font-size: 16px;
            padding: 8px 12px; border-radius: 5px;
        }
        .nav-links a:hover {
            background: #555;
        }
        .auth-icons {
            display: flex; gap: 15px;
        }
        .auth-icons a {
            color: white; text-decoration: none; font-size: 16px;
        }
        .header {
            background: #333; color: white; padding: 15px;
            font-size: 24px; font-weight: bold; position: relative;
        }
        .user-email {
            position: absolute; top: 15px; right: 20px;
            font-size: 16px; color: #ffcc00;
        }
        .container {
            padding: 40px; background: white;
            margin: 50px auto; width: 60%;
            border-radius: 8px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            flex-grow: 1; display: flex; flex-direction: column; align-items: center;
        }
        .input-field {
            width: 80%; padding: 10px; margin: 10px;
            border: 1px solid #ccc; border-radius: 5px;
        }
        .btn {
            background: #ff6600; color: white;
            padding: 10px 20px; border: none;
            border-radius: 5px; cursor: pointer; font-size: 16px;
        }
        .footer {
            background: #333; color: white; padding: 20px;
            text-align: center; margin-top: auto;
        }
        .logout-btn {
            margin-left: 10px;
            padding: 5px 10px;
            background: red;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
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
            <?php if ($loggedInUser): ?>
                <span>👋 Welcome, <?= htmlspecialchars($loggedInUser) ?></span>
                <form method="post" action="logout.php" style="display:inline;">
                    <button class="logout-btn" type="submit">Logout</button>
                </form>
            <?php else: ?>
                <a href="login.php">🔑 Login</a>
                <a href="signup.php">📝 Sign Up</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="header">
        Contact Us - KVS Online Auction
        <div class="user-email">
            <?= $loggedInUser ? "Logged in as: " . htmlspecialchars($loggedInUser) : "Not Logged In" ?>
        </div>
    </div>

    <div class="container">
        <h2 style="color: #333;">Get in Touch</h2>
        <p style="font-size: 18px; color: #555;">Have questions? Reach out to us.</p>

        <form method="POST" style="display: flex; flex-direction: column; align-items: center;">
            <input type="text" name="name" placeholder="Your Name" required class="input-field">
            <input type="email" name="email" placeholder="Your Email" required class="input-field">
            <textarea name="message" placeholder="Your Message" required class="input-field" style="height: 100px;"></textarea>
            <button type="submit" class="btn">Submit</button>
        </form>

        <button class="btn" onclick="window.location.href='home.php'">Go Home</button>
    </div>

    <div style="margin-top: 20px; font-size: 18px; text-align: center;">
        <p><strong>Email:</strong> support@kvsauction.com</p>
        <p><strong>Phone:</strong> +1 234 567 890</p>
        <p><strong>Address:</strong> 123 Auction Street, Melbourne, Australia</p>
    </div>

    <div style="margin-top: 40px; text-align: center;">
        <iframe src="https://www.google.com/maps/embed?pb=..." width="80%" height="300" style="border:0; border-radius: 8px;" allowfullscreen loading="lazy"></iframe>
    </div>

    <div class="footer">
        &copy; 2025 KVS Online Auction System. All Rights Reserved.
    </div>

</body>
</html>
