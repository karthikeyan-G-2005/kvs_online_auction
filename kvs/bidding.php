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

$success_message = "";
$error_message = "";

// Handle bid submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_bid'])) {
    $item_id = (int)$_POST['item_id'];
    $bid_amount = (float)$_POST['bid_amount'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT starting_price, end_time FROM auction_items WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $starting_price = $row['starting_price'];
        $end_time = $row['end_time'];

        if (new DateTime() > new DateTime($end_time)) {
            $error_message = "Sorry, this auction has ended. You can no longer place bids.";
        } elseif ($bid_amount > $starting_price) {
            $insert = $conn->prepare("INSERT INTO bids (item_id, user_id, bid_amount, bid_time) VALUES (?, ?, ?, NOW())");
            $insert->bind_param("iid", $item_id, $user_id, $bid_amount);
            $insert->execute();

            $update = $conn->prepare("UPDATE auction_items SET starting_price = ? WHERE id = ?");
            $update->bind_param("di", $bid_amount, $item_id);
            $update->execute();

            $success_message = "Bid placed successfully!";
        } else {
            $error_message = "Bid must be greater than the current price.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>KVS Online Auction System - Bidding</title>
  <style>
    /* Global Styles */
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
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }
    .bid-details {
      margin-bottom: 1rem;
    }
    .bid-actions {
      display: flex;
      align-items: center;
    }
    .bid-actions input[type="number"] {
      padding: 0.5rem;
      margin-right: 10px;
      width: 120px;
    }
    .bid-btn {
      padding: 0.5rem 1rem;
      background-color: #005792;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .bid-btn:hover {
      background-color: #003f6b;
    }
    .create-bid-link {
      display: inline-block;
      margin-top: 20px;
      padding: 0.5rem 1rem;
      background-color: #007BFF;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }
    .create-bid-link:hover {
      background-color: #0056b3;
    }
    .no-bids-message {
      color: #888;
      font-style: italic;
    }
    .logout-btn {
      display: inline-block;
      margin-top: 10px;
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
    .countdown {
      font-size: 1.2rem;
      color: red;
      font-weight: bold;
    }
    /* Image styling */
    .bid-item img {
      max-width: 300px;
      height: auto;
      border-radius: 10px;
      display: block;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

<div class="navbar">
  <div class="nav-links">
    <a href="home.php">Home</a>
    <a href="bidding.php">Bidding</a>
    <a href="view_bid.php">your bids</a>
    <a href="bidcreate.php">Create Bid</a>
    <a href="contact.php">Contact</a>
    <a href="complaint.php">Complaint</a>
    <a href="feedback.php">Feedback</a>
  </div>
  <div class="auth-icons">
    <span style="color: white;">👋 Welcome, <?= htmlspecialchars($_SESSION['username']); ?></span>
  </div>
</div>

<div class="container">
  <h2>KVS Available Bids</h2>

  <?php if (!empty($success_message)) echo "<p style='color:green;'>$success_message</p>"; ?>
  <?php if (!empty($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>

  <div id="bids-container">
    <?php
    $result = $conn->query("SELECT * FROM auction_items");
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $itemId = $row['id'];
        $title = htmlspecialchars($row['title']);
        $description = htmlspecialchars($row['description']);
        $price = $row['starting_price'];
        $end_time = $row['end_time'];
        
        // Get product image (assuming column name is 'image') and create file path.
        // If no image is provided, use a fallback "default.jpg".
        $imageFile = $row['image'];
        $imagePath = (!empty($imageFile)) ? "uploads/" . $imageFile : "uploads/default.jpg";

        // Get current highest bid and number of bidders
        $highestBidResult = $conn->query("SELECT MAX(bid_amount) as max_bid FROM bids WHERE item_id = $itemId");
        $highestBid = $highestBidResult->fetch_assoc()['max_bid'] ?? $price;

        $numBiddersResult = $conn->query("SELECT COUNT(DISTINCT user_id) as num_bidders FROM bids WHERE item_id = $itemId");
        $numBidders = $numBiddersResult->fetch_assoc()['num_bidders'] ?? 0;

        // Convert end time to a JavaScript-friendly format (ISO 8601)
        $end_time_js = (new DateTime($end_time))->format('Y-m-d\TH:i:s');

        echo "
        <div class='bid-item'>
          <h3>$title</h3>
          <img src='$imagePath' alt='Product Image'>
          <div class='bid-details'>
            <p>Starting Price: ₹" . number_format($price, 2) . "</p>
            <p>Description: $description</p>
            <p>Current Highest Bid: ₹" . number_format($highestBid, 2) . "</p>
            <p>Number of Bidders: $numBidders</p>
            <p>Time Remaining: <span class='countdown' id='countdown_$itemId'></span></p>
          </div>";

        // If auction not ended, show the bid form; otherwise, show notice.
        $isExpired = new DateTime() > new DateTime($end_time);
        if (!$isExpired) {
          echo "
          <form method='POST' class='bid-actions'>
            <input type='hidden' name='item_id' value='$itemId'>
            <input type='number' step='0.01' name='bid_amount' placeholder='Your Bid' required>
            <button class='bid-btn' type='submit' name='place_bid'>Place Bid</button>
          </form>";
        } else {
          echo "<p style='color: red; font-weight: bold;'>Auction has ended.</p>";
        }

        echo "</div>";

        // JavaScript for live countdown timer per auction item
        echo "
        <script>
          (function() {
            var endTime = new Date('$end_time_js').getTime();
            var countdownElement = document.getElementById('countdown_$itemId');

            function updateCountdown() {
              var now = new Date().getTime();
              var timeLeft = endTime - now;

              if (timeLeft <= 0) {
                countdownElement.innerHTML = 'Auction Ended';
              } else {
                var hours = Math.floor(timeLeft / (1000 * 60 * 60));
                var minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                countdownElement.innerHTML = hours + 'h ' + minutes + 'm ' + seconds + 's';
              }
            }
            updateCountdown();
            setInterval(updateCountdown, 1000);
          })();
        </script>";
      }
    } else {
      echo "<p class='no-bids-message'>No bids available yet.</p>";
    }
    ?>
  </div>

  <a href="bidcreate.php" class="create-bid-link">Create New Bid</a>
  <a href="logout.php" class="logout-btn">Logout</a>
</div>

<div class="footer">
  &copy; 2025 KVS Online Auction System. All Rights Reserved.<br>
  For help contact us via <a href="contact.php">Contact</a> |
  <a href="complaint.php">File a Complaint</a> |
  <a href="feedback.php">Send Feedback</a>
</div>

</body>
</html>
