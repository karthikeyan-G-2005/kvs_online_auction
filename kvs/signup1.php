<?php
// Database credentials
$host = "localhost";
$dbname = "kvs_auction";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form POST submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with($email, '@gmail.com')) {
        echo "<script>alert('Invalid email. Only @gmail.com emails allowed.'); window.location.href='signup.php';</script>";
        exit;
    }

    // Check if email already registered
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<script>alert('This email is already registered.'); window.location.href='signup.php';</script>";
        exit;
    }

    // Hash and insert
    $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);
    $insertStmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $insertStmt->bind_param("sss", $user, $email, $hashedPassword);

    if ($insertStmt->execute()) {
        echo "<script>alert('Signup successful! Redirecting to login...'); window.location.href='login.php';</script>";
        exit;
    } else {
        echo "<script>alert('Signup failed. Please try again.'); window.location.href='signup.php';</script>";
    }

    $insertStmt->close();
    $checkStmt->close();
}

$conn->close();
?>
<!-- HTML FORM -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Signup | KVS Auction</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 400px;
        }

        .login-box {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .input-box {
            margin-bottom: 20px;
        }

        .input-box input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
        }

        .input-box label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            background-color: #28a745;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #218838;
        }

        p {
            text-align: center;
            margin-top: 15px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function validateSignupForm() {
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();
            const username = document.getElementById("username").value.trim();

            if (!email || !password || !username) {
                alert("All fields are required.");
                return false;
            }

            if (!email.endsWith("@gmail.com")) {
                alert("Only @gmail.com emails are allowed.");
                return false;
            }

            return true;
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="login-box">
            <h2>Signup</h2>
            <form id="signupForm" action="signup.php" method="POST" onsubmit="return validateSignupForm();">
                <div class="input-box">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="input-box">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="input-box">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit" class="btn">Signup</button>
            </form>
            <p>Already have an account? <a href="login.php">Login Here</a></p>
        </div>
    </div>
</body>

</html>
