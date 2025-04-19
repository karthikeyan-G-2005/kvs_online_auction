<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'kvs_auction';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Deleting users
if (isset($_GET['delete_user'])) {
    $deleteId = $_GET['delete_user'];
    $conn->query("DELETE FROM users WHERE id=$deleteId");
}

// Deleting products
if (isset($_GET['delete_product'])) {
    $deleteId = $_GET['delete_product'];
    $conn->query("DELETE FROM auction_items WHERE id=$deleteId");
}

// Fetch the counts
$userCount = $conn->query("SELECT COUNT(*) as total_users FROM users")->fetch_assoc()['total_users'];
$bidCount = $conn->query("SELECT COUNT(*) as total_bids FROM auction_items")->fetch_assoc()['total_bids'];
$biddingCount = $conn->query("SELECT COUNT(*) as total_biddings FROM bids")->fetch_assoc()['total_biddings'];

// Fetch winners for each product
$winnersQuery = "
    SELECT 
        a.id AS item_id, 
        a.title AS item_title, 
        u.username AS winner_username, 
        u.email AS winner_email, 
        b.bid_amount AS winning_bid 
    FROM auction_items a
    JOIN bids b ON a.id = b.item_id
    JOIN users u ON b.user_id = u.id
    WHERE b.bid_amount = (SELECT MAX(bid_amount) FROM bids WHERE item_id = a.id)
";
$winners = $conn->query($winnersQuery);

// Fetch feedbacks (from product_feedback table)
$feedbackQuery = "SELECT * FROM product_feedback";
$feedbacks = $conn->query($feedbackQuery);

// Fetch contacts (from contact_messages table)
$contactQuery = "SELECT * FROM contact_messages";
$contacts = $conn->query($contactQuery);

// Fetch complaints (from complaints table)
$complaintQuery = "SELECT * FROM complaints";
$complaints = $conn->query($complaintQuery);

// Fetch auction items for the Create Bid Management section
$itemsQuery = "SELECT id, title, description, starting_price, end_time, created_by, image FROM auction_items";
$itemsResult = $conn->query($itemsQuery);

// Fetch bids for the Bidding Management section
$biddingQuery = "
    SELECT b.id, u.username AS user_name, a.title AS item_title, b.bid_amount, b.bid_time 
    FROM bids b 
    JOIN auction_items a ON b.item_id = a.id 
    JOIN users u ON b.user_id = u.id
";
$biddingResult = $conn->query($biddingQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            min-height: 100vh;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #333;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(to bottom, #2c3e50, #4ca1af);
            color: white;
            padding-top: 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.2);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #f1c40f;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            padding: 15px 20px;
            border-bottom: 1px solid #34495e;
        }

        .sidebar ul li:hover, .sidebar ul li.active {
            background-color: #16a085;
            cursor: pointer;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }

        .main {
            flex: 1;
            padding: 30px;
            background-color: #ecf0f1;
        }

        .content-section {
            display: none;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .content-section.active {
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: #f9f9f9;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #2980b9;
            color: white;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-box {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 30%;
        }

        .stat-box h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .stat-box p {
            font-size: 22px;
            font-weight: bold;
        }
    </style>
    <script>
        function showSection(sectionId) {
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => section.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');

            const menuItems = document.querySelectorAll('.sidebar ul li');
            menuItems.forEach(item => item.classList.remove('active'));

            const activeMenuItem = document.querySelector(`.sidebar ul li[data-section='${sectionId}']`);
            if (activeMenuItem) {
                activeMenuItem.classList.add('active');
            }
        }

        window.onload = () => showSection('dashboard');
    </script>
</head>
<body>
<div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
        <li data-section="dashboard" onclick="showSection('dashboard')">Dashboard</li>
        <li data-section="users" onclick="showSection('users')">User Management</li>
        <li data-section="create-bid" onclick="showSection('create-bid')">Create Bid Management</li>
        <li data-section="bidding" onclick="showSection('bidding')">Bidding Management</li>
        <li data-section="winners" onclick="showSection('winners')">Winners</li>
        <li data-section="feedbacks" onclick="showSection('feedbacks')">Feedback List</li>
        <li data-section="contacts" onclick="showSection('contacts')">Contact List</li>
        <li data-section="complaints" onclick="showSection('complaints')">Complaint List</li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="main">
    <!-- Dashboard Section -->
    <div id="dashboard" class="content-section active">
        <h1>Welcome Admin 👋</h1>
        <p>This is your dashboard overview. Use the left menu to manage the platform.</p>
        
        <div class="stats">
            <div class="stat-box">
                <h3>Total Users</h3>
                <p><?php echo $userCount; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Bids Created</h3>
                <p><?php echo $bidCount; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Biddings</h3>
                <p><?php echo $biddingCount; ?></p>
            </div>
        </div>
    </div>

    <!-- User Management Section -->
    <div id="users" class="content-section">
        <h2>User Management</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Date Joined</th>
                <th>Action</th>
            </tr>
            <?php
            $users = $conn->query("SELECT * FROM users");
            while ($user = $users->fetch_assoc()) {
                $dateJoined = isset($user['created_at']) ? date('Y-m-d H:i:s', strtotime($user['created_at'])) : 'N/A';
                echo "<tr>
                    <td>{$user['id']}</td>
                    <td>{$user['username']}</td>
                    <td>{$user['email']}</td>
                    <td>{$dateJoined}</td>
                    <td>
                        <a href='admin_dashboard.php?delete_user={$user['id']}' class='delete-btn' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                    </td>
                </tr>";
            }
            ?>
        </table>
    </div>

    <!-- Create Bid Management Section -->
    <div id="create-bid" class="content-section">
        <h2>Create Bid Management</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Item Name</th>
                <th>Description</th>
                <th>Start Price</th>
                <th>Action</th>
            </tr>
            <?php
            if ($itemsResult && $itemsResult->num_rows > 0) {
                while ($item = $itemsResult->fetch_assoc()) {
                    $startingPrice = isset($item['starting_price']) ? $item['starting_price'] : 'N/A';
                    echo "<tr>
                        <td>{$item['id']}</td>
                        <td>{$item['title']}</td>
                        <td>{$item['description']}</td>
                        <td>{$startingPrice}</td>
                        <td>
                            <a href='admin_dashboard.php?delete_product={$item['id']}' class='delete-btn' onclick='return confirm(\"Delete this product?\")'>Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No auction items available.</td></tr>";
            }
            ?>
        </table>
    </div>

    <!-- Bidding Management Section -->
    <div id="bidding" class="content-section">
        <h2>Bidding Management</h2>
        <table>
            <tr>
                <th>Bid ID</th>
                <th>Username</th>
                <th>Bid Amount</th>
                <th>Item Title</th>
                <th>Bid Time</th>
            </tr>
            <?php
            if ($biddingResult && $biddingResult->num_rows > 0) {
                while ($bid = $biddingResult->fetch_assoc()) {
                    $formattedTime = isset($bid['bid_time']) ? date('Y-m-d H:i:s', strtotime($bid['bid_time'])) : 'N/A';
                    echo "<tr>
                        <td>{$bid['id']}</td>
                        <td>{$bid['user_name']}</td>
                        <td>{$bid['bid_amount']}</td>
                        <td>{$bid['item_title']}</td>
                        <td>{$formattedTime}</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No bids available.</td></tr>";
            }
            ?>
        </table>
    </div>

    <!-- Winners Section -->
    <div id="winners" class="content-section">
        <h2>Winners</h2>
        <table>
            <tr>
                <th>Item ID</th>
                <th>Item Title</th>
                <th>Winner</th>
                <th>Winning Bid</th>
            </tr>
            <?php
            while ($winner = $winners->fetch_assoc()) {
                echo "<tr>
                    <td>{$winner['item_id']}</td>
                    <td>{$winner['item_title']}</td>
                    <td>{$winner['winner_username']} ({$winner['winner_email']})</td>
                    <td>{$winner['winning_bid']}</td>
                </tr>";
            }
            ?>
        </table>
    </div>

    <!-- Feedback List Section -->
    <div id="feedbacks" class="content-section">
        <h2>Feedback List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Rating</th>
                <th>Feedback</th>
                <th>Date</th>
            </tr>
            <?php
            while ($feedback = $feedbacks->fetch_assoc()) {
                $submitted_at = isset($feedback['submitted_at']) ? $feedback['submitted_at'] : 'N/A';
                echo "<tr>
                    <td>{$feedback['id']}</td>
                    <td>{$feedback['product']}</td>
                    <td>{$feedback['rating']}</td>
                    <td>{$feedback['feedback']}</td>
                    <td>{$submitted_at}</td>
                </tr>";
            }
            ?>
        </table>
    </div>

    <!-- Contact List Section -->
    <div id="contacts" class="content-section">
        <h2>Contact List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Date</th>
            </tr>
            <?php
            while ($contact = $contacts->fetch_assoc()) {
                $created_at = isset($contact['created_at']) ? $contact['created_at'] : 'N/A';
                echo "<tr>
                    <td>{$contact['id']}</td>
                    <td>{$contact['name']}</td>
                    <td>{$contact['email']}</td>
                    <td>{$contact['message']}</td>
                    <td>{$created_at}</td>
                </tr>";
            }
            ?>
        </table>
    </div>

    <!-- Complaint List Section -->
    <div id="complaints" class="content-section">
        <h2>Complaint List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Order ID</th>
                <th>Complaint</th>
                <th>Date</th>
            </tr>
            <?php
            while ($complaint = $complaints->fetch_assoc()) {
                $created_at = isset($complaint['created_at']) ? $complaint['created_at'] : 'N/A';
                // Adjust the field name for the complaint message if it's named differently in your table
                $complaintMessage = isset($complaint['complaint']) ? $complaint['complaint'] : 'No complaint message';
                echo "<tr>
                    <td>{$complaint['id']}</td>
                    <td>{$complaint['name']}</td>
                    <td>{$complaint['email']}</td>
                    <td>{$complaint['order_id']}</td>
                    <td>{$complaintMessage}</td>
                    <td>{$created_at}</td>
                </tr>";
            }
            ?>
        </table>
    </div>
</div>
</body>
</html>

<?php
$conn->close();
?>
