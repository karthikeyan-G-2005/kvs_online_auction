<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "kvs_auction");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetching all the bids the user has placed
$query_bids = "SELECT ai.title, ai.description, ai.starting_price, ai.end_time, b.bid_amount, b.bid_time, b.id AS bid_id
               FROM bids b
               JOIN auction_items ai ON b.item_id = ai.id
               WHERE b.user_id = $user_id
               ORDER BY b.bid_time DESC";

$result_bids = $conn->query($query_bids);

// Fetching won items (where bid is the highest)
$query_wins = "SELECT ai.title, ai.description, ai.starting_price, ai.end_time, MAX(b.bid_amount) AS winning_bid
               FROM bids b
               JOIN auction_items ai ON b.item_id = ai.id
               WHERE b.user_id = $user_id
               GROUP BY ai.id
               HAVING MAX(b.bid_amount) = ai.starting_price";

$result_wins = $conn->query($query_wins);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Your Bids - KVS Online Auction</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: #f9f9f9;
        }
        .navbar {
            background-color: rgb(20, 20, 20);
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
            font-weight: bold;
        }
        .nav-links a:hover {
            text-decoration: underline;
        }
        .auth-icons {
            display: flex;
            align-items: center;
        }
        .container {
            padding: 2rem;
        }
        h2 {
            color: #005792;
            margin-bottom: 2rem;
        }
        .bid-item {
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .bid-details {
            margin-bottom: 1rem;
        }
        .logout-btn {
            display: inline-block;
            margin-left: 15px; /* Space between the username and the logout button */
            padding: 0.5rem 1rem;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        .footer {
            text-align: center;
            padding: 1rem;
            background-color: #f1f1f1;
            margin-top: 2rem;
            border-top: 1px solid #ccc;
        }
        .footer a {
            color: #005792;
            text-decoration: none;
            margin: 0 10px;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="bidding.php">Bidding</a>
        <a href="view_bids.php">Your Bids</a>
        <a href="contact.php">Contact</a>
        <a href="complaint.php">Complaint</a>
        <a href="feedback.php">Feedback</a>
    </div>
    <div class="auth-icons">
        <span style="color: white;">👋 Welcome, <?= htmlspecialchars($_SESSION['username']); ?></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Your Bids</h2>

    <h3>Bids You Have Placed</h3>
    <?php
    if ($result_bids->num_rows > 0) {
        while ($row = $result_bids->fetch_assoc()) {
            $title = htmlspecialchars($row['title']);
            $description = htmlspecialchars($row['description']);
            $starting_price = $row['starting_price'];
            $bid_amount = $row['bid_amount'];
            $end_time = $row['end_time'];

            echo "
            <div class='bid-item'>
                <h3>$title</h3>
                <div class='bid-details'>
                    <p>Starting Price: ₹" . number_format($starting_price, 2) . "</p>
                    <p>Description: $description</p>
                    <p>Your Bid: ₹" . number_format($bid_amount, 2) . "</p>
                    <p>Bid Time: " . date("Y-m-d H:i:s", strtotime($row['bid_time'])) . "</p>
                    <p>Time Remaining: " . (new DateTime() > new DateTime($end_time) ? 'Auction Ended' : (new DateTime($end_time))->format('Y-m-d H:i:s')) . "</p>
                </div>
            </div>
            ";
        }
    } else {
        echo "<p>You haven't placed any bids yet.</p>";
    }
    ?>

    <h3>Items You Have Won</h3>
    <?php
    if ($result_wins->num_rows > 0) {
        while ($row = $result_wins->fetch_assoc()) {
            $title = htmlspecialchars($row['title']);
            $description = htmlspecialchars($row['description']);
            $starting_price = $row['starting_price'];

            echo "
            <div class='bid-item'>
                <h3>$title</h3>
                <div class='bid-details'>
                    <p>Starting Price: ₹" . number_format($starting_price, 2) . "</p>
                    <p>Description: $description</p>
                    <p>Your Winning Bid: ₹" . number_format($row['winning_bid'], 2) . "</p>
                </div>
            </div>
            ";
        }
    } else {
        echo "<p>You haven't won any auctions yet.</p>";
    }
    ?>

</div>

<div class="footer">
    &copy; 2025 KVS Online Auction System. All Rights Reserved.<br>
    For help contact us via <a href="contact.php">Contact</a> |
    <a href="complaint.php">File a Complaint</a> |
    <a href="feedback.php">Send Feedback</a>
</div>

</body>
</html>
