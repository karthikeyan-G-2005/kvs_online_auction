<?php
session_start();
$loggedInUser = $_SESSION['email'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product = $_POST["product"];
    $rating = $_POST["rating"];
    $feedback = $_POST["feedback"];
    $user = $loggedInUser ?? $_POST["email"];

    // DB Connection
    $conn = new mysqli("localhost", "root", "", "kvs_auction");

    if ($conn->connect_error) {
        die("Connection Failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO product_feedback (product, rating, feedback, user_email) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siss", $product, $rating, $feedback, $user);

    if ($stmt->execute()) {
        echo "<script>alert('Thank you for your feedback!'); window.location.href='home.php';</script>";
    } else {
        echo "<script>alert('Error saving feedback.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Feedback - KVS Online Auction</title>
    <style>
        /* Same CSS as your original */
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
            display: flex; justify-content: space-between; align-items: center;
            background: #333; padding: 15px; color: white;
        }
        .nav-links {
            display: flex; gap: 20px;
        }
        .nav-links a {
            color: white; text-decoration: none;
            font-size: 16px; padding: 8px 12px; border-radius: 5px;
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
        .container {
            width: 40%; margin: 40px auto;
            background: white; padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
        }
        h2 { text-align: center; margin-bottom: 20px; }
        label { font-weight: bold; margin-top: 10px; display: block; }
        input, textarea {
            width: 100%; padding: 10px;
            margin: 8px 0; border: 1px solid #ccc;
            border-radius: 5px; box-sizing: border-box;
        }
        .rating {
            direction: rtl; display: flex;
            justify-content: center; gap: 5px;
        }
        .rating input { display: none; }
        .rating label {
            font-size: 30px; color: gray; cursor: pointer; transition: color 0.2s;
        }
        .rating input:checked ~ label,
        .rating input:checked + label { color: gold; }
        .rating label:hover,
        .rating label:hover ~ label { color: gold; }
        button {
            width: 100%; background: #28a745;
            color: white; border: none;
            padding: 12px; font-size: 16px;
            cursor: pointer; border-radius: 5px; margin-top: 10px;
        }
        button:hover { background: #218838; }
        .footer {
            margin-top: 40px;
            background: #333;
            color: white;
            padding: 20px;
            font-size: 16px;
            text-align: center;
        }
        .home-btn {
            background: #007bff; color: white;
            border: none; padding: 10px 15px;
            font-size: 16px; cursor: pointer;
            border-radius: 5px; margin-top: 20px;
            text-decoration: none; display: inline-block;
        }
        .home-btn:hover { background: #0056b3; }
        .logout-btn {
            margin-left: 10px; padding: 5px 10px;
            background: red; color: white;
            border: none; cursor: pointer;
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

    <div class="container">
        <h2>Product Feedback</h2>
        <form method="POST">
            <label for="product">Product Name</label>
            <input type="text" name="product" id="product" required>

            <label>Rate the Product</label>
            <div class="rating">
                <input type="radio" id="star5" name="rating" value="5"><label for="star5">⭐</label>
                <input type="radio" id="star4" name="rating" value="4"><label for="star4">⭐</label>
                <input type="radio" id="star3" name="rating" value="3"><label for="star3">⭐</label>
                <input type="radio" id="star2" name="rating" value="2"><label for="star2">⭐</label>
                <input type="radio" id="star1" name="rating" value="1"><label for="star1">⭐</label>
            </div>

            <label for="feedback">Your Feedback</label>
            <textarea name="feedback" id="feedback" rows="4" required></textarea>

            <?php if (!$loggedInUser): ?>
                <label for="email">Your Email</label>
                <input type="email" name="email" id="email" required>
            <?php endif; ?>

            <button type="submit">Submit Feedback</button>
        </form>

        <a href="home.php" class="home-btn">Go Home</a>
    </div>

    <div class="footer">
        &copy; 2025 KVS Online Auction System. All Rights Reserved.
    </div>

</body>
</html>
