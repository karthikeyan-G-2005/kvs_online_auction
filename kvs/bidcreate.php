<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "kvs_auction");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["itemName"]);
    $starting_price = floatval($_POST["startBid"]);
    $description = trim($_POST["description"]);
    $end_time = $_POST["endTime"];
    $created_by = $_SESSION['username'];
    
    // Handle image upload
    $image = $_FILES['productImage'];
    $imageName = "";
    if ($image['error'] == 0) {
        $targetDir = "uploads/"; // Directory to store the uploaded images
        $imageName = basename($image["name"]);
        $targetFilePath = $targetDir . $imageName;
        
        // Check if image file is an actual image
        $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];
        if (in_array($imageFileType, $allowedTypes)) {
            // Upload the image to the server
            if (!move_uploaded_file($image["tmp_name"], $targetFilePath)) {
                $error = "Sorry, there was an error uploading your image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    }

    // Insert data into the database
    if (!empty($title) && $starting_price >= 0 && !empty($end_time)) {
        $stmt = $conn->prepare("INSERT INTO auction_items (title, description, starting_price, end_time, created_by, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsss", $title, $description, $starting_price, $end_time, $created_by, $imageName);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: bidding.php");
            exit();
        } else {
            $error = "Failed to create item. Please try again.";
        }
        $stmt->close();
    } else {
        $error = "Please fill in all required fields correctly.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KVS Online Auction System - Create New Bid</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #222;
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

        .container {
            background: #1c1c1c;
            border-radius: 8px;
            padding: 40px;
            width: 80%;
            max-width: 600px;
            margin: 20px auto;
            box-sizing: border-box;
            flex-grow: 1;
            color: #fff;
        }

        h2 {
            margin: 0 0 30px;
            padding: 0;
            text-align: center;
            color: #fff;
        }

        .input-box {
            position: relative;
            margin-bottom: 20px;
        }

        .input-box input {
            width: 100%;
            padding: 10px 0;
            font-size: 16px;
            color: #fff;
            border: none;
            border-bottom: 1px solid #fff;
            outline: none;
            background: transparent;
        }

        .input-box label {
            position: absolute;
            top: 0;
            left: 0;
            padding: 10px 0;
            font-size: 16px;
            color: #fff;
            pointer-events: none;
            transition: .5s;
        }

        .input-box input:focus ~ label,
        .input-box input:valid ~ label {
            top: -20px;
            left: 0;
            color: #ffcc00;
            font-size: 12px;
        }

        .btn {
            width: 100%;
            padding: 10px 0;
            font-size: 16px;
            color: #1c1c1c;
            background: #ffcc00;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background: #e6b800;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            color: #fff;
            text-align: center;
            text-decoration: none;
        }

        .back-link:hover {
            color: #ffcc00;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background: #333;
            color: white;
            font-size: 16px;
            margin-top: 20px;
        }

        .footer a {
            color: #ff6600;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            text-align: center;
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
            <a href="logout.php" style="color: white;">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Create New Bid</h2>
        <?php if (isset($error)) echo "<div class='error-message'>$error</div>"; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="input-box">
                <input type="text" name="itemName" required>
                <label>Item Name</label>
            </div>
            <div class="input-box">
                <input type="number" name="startBid" min="0" step="0.01" required>
                <label>Starting Bid</label>
            </div>
            <div class="input-box">
                <input type="text" name="description">
                <label>Description (Optional)</label>
            </div>
            <div class="input-box">
                <input type="datetime-local" name="endTime" required>
                <label>Ending Time</label>
            </div>
            <div class="input-box">
                <input type="file" name="productImage" accept="image/*" required>
                <label>Upload Product Image</label>
            </div>
            <button type="submit" class="btn">Create Bid</button>
        </form>
        <a href="bidding.php" class="back-link">Back to Bidding Page</a>
    </div>

    <div class="footer">
        &copy; 2025 KVS Online Auction System. All Rights Reserved.<br>
        For help contact us via <a href="contact.php">Contact</a> | <a href="complaint.php">File a Complaint</a> | <a href="feedback.php">Send Feedback</a>
    </div>
</body>
</html>
