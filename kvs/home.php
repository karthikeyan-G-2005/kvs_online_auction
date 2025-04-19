<?php
session_start();
$loggedInUser = isset($_SESSION['username']) ? $_SESSION['username'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>KVS Auction Home</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: url('bidding.png') no-repeat center center fixed;
      background-size: cover;
      color: white;
    }

    header {
      background: rgba(0, 0, 0, 0.7);
      padding: 10px 0;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: auto;
      padding: 0 20px;
    }

    .nav-links, .user-section {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .nav-links a, .user-section a {
      text-decoration: none;
      color: #03e9f4;
      font-weight: bold;
      padding: 10px 15px;
      border-radius: 5px;
      transition: 0.3s;
    }

    .nav-links a:hover, .user-section a:hover {
      background: #03e9f4;
      color: #000;
    }

    .logout-btn {
      background-color: red;
      border: none;
      color: white;
      padding: 8px 12px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }

    .hero {
      text-align: center;
      margin-top: 100px;
    }

    .hero h1 {
      font-size: 3rem;
      margin-bottom: 20px;
      text-shadow: 2px 2px 8px #000;
    }

    .hero .btn {
      background: #98f403;
      color: #000;
      padding: 20px 40px;
      font-size: 1.5rem;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      text-decoration: none;
      transition: 0.3s;
    }

    .hero .btn:hover {
      background: #f9faf7;
    }

    section {
      background-color: rgba(0, 0, 0, 0.6);
      padding: 40px 20px;
      margin: 40px 10%;
      border-radius: 10px;
      box-shadow: 0 0 10px #03e9f4;
    }

    marquee {
      color: #000;
      font-weight: bold;
      padding: 10px;
      font-size: 1.1rem;
    }

    footer {
      text-align: center;
      background: rgba(0, 0, 0, 0.8);
      padding: 15px;
      position: relative;
      bottom: 0;
      width: 100%;
    }
  </style>
</head>
<body>

  <header>
    <div class="navbar">
      <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="bidding.php">Bidding</a>
        <a href="view_bid.php">your bids</a>
        <a href="bidcreate.php">Bid Create</a>
        <a href="complaint.php">Complaint</a>
        <a href="feedback.php">Feedback</a>
        <a href="contact.php">Contact</a>
      </div>
      <div class="user-section">
        <?php if ($loggedInUser): ?>
          <span style="color:white; font-weight:bold;">👤 <?= htmlspecialchars($loggedInUser) ?></span>
          <form method="post" action="logout.php" style="display:inline;">
            <button class="logout-btn" type="submit">Logout</button>
          </form>
        <?php else: ?>
          <a href="login.php">Login</a>
          <a href="signup.php">Signup</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <marquee behavior="scroll" direction="left">
    🔥 Upcoming Events: 🚀 Summer Electronics Auction - April 10 | 🖼️ Art & Antiques Special - April 18 | 🛍️ Mega Deals Week - April 25!
  </marquee>

  <div class="hero">
    <h1>Welcome to KVS Auction</h1>
    <a href="bidding.php" class="btn">View Auctions</a>
  </div>

  <!-- About Sections and Features... (unchanged) -->
  <!-- You can keep all your <section> blocks below as-is -->

  <section>
    <h2>About Our Website</h2>
    <p>BidZone is your one-stop platform for online auctions. Whether you're a buyer looking for great deals or a seller wanting to reach more customers, BidZone brings powerful features, a clean interface, and a secure experience for all users.</p>
  </section>

  <section>
    <h2>Featured Auctions</h2>
    <ul>
      <li>🔧 Industrial Tools Auction - April 8</li>
      <li>🏡 Real Estate Mega Sale - April 14</li>
      <li>🚗 Classic Cars Collection - April 22</li>
      <li>📚 Rare Book Festival - April 30</li>
    </ul>
  </section>

  <section>
    <h2>Why Choose KVS Auction?</h2>
    <ul>
      <li>✅ Transparent and secure bidding process</li>
      <li>✅ Wide variety of auction categories</li>
      <li>✅ Easy listing for sellers</li>
      <li>✅ 24/7 customer support</li>
      <li>✅ Mobile-friendly interface</li>
    </ul>
  </section>

  <section>
    <h2>What Our Users Say</h2>
    <blockquote>“I sold my old bike in under 3 hours! The platform is easy and intuitive.”<br><strong>- Suresh R.</strong></blockquote>
    <blockquote>“Found rare collectibles I’ve been searching for—highly recommend!”<br><strong>- Divya M.</strong></blockquote>
  </section>

  <section>
    <h2>How This Website is Useful</h2>
    <ul>
      <li>🌟 Easy-to-use auction listing</li>
      <li>🔐 Secure login and bidding process</li>
      <li>💬 Feedback & Complaint management</li>
      <li>📱 Accessible across all devices</li>
      <li>📊 Real-time updates and notifications</li>
    </ul>
  </section>

  <section>
    <h2>Latest Announcements</h2>
    <ul>
      <li>📣 New Seller Portal Launched - Easier Listing!</li>
      <li>📣 Introducing Verified Auctions for Trusted Sellers</li>
      <li>📣 Android App Launching Soon - Stay Tuned</li>
    </ul>
  </section>

  <footer>
    &copy; 2025 BidZone. All Rights Reserved.
    <h2>Need Help?</h2>
    <p>If you need assistance, we’re here to help!</p>
    <ul>
      📧 Email: kvsonlineauction@gmail.com<br>
      📞 Phone: +91 98765 43210<br>
      💬 Live Chat: Available 9AM - 9PM IST
    </ul>
  </footer>

</body>
</html>
