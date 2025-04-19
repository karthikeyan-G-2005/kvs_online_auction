<?php
// PHP Login Code - Keep this part exactly as you already have
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_email = "admin@kvs.com";
    $admin_password = "admin123";

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['admin'] = true;
        $_SESSION['username'] = "Admin";
        header("Location: admin_dashboard.php");
        exit();
    }

    $conn = new mysqli('localhost', 'root', '', 'kvs_auction');
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            header("Location: bidding.php");
            exit();
        } else {
            header("Location: login.php?error=password");
            exit();
        }
    } else {
        header("Location: login.php?error=email");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>


<!-- HTML Part -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | KVS Auction</title>
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

        .login-box {
            background: #fff;
            padding: 40px;
            width: 350px;
            box-shadow: 0px 8px 20px rgba(0,0,0,0.1);
            border-radius: 10px;
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
            background-color: #007bff;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .link-text {
            text-align: center;
            margin-top: 15px;
        }

        .link-text a {
            color: #007bff;
            text-decoration: none;
        }

        .link-text a:hover {
            text-decoration: underline;
        }
    </style>

    <script>
        function validateForm() {
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();

            if (!email || !password) {
                alert("Please enter both email and password.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>
        <form id="loginForm" action="login.php" method="POST" onsubmit="return validateForm();">
            <div class="input-box">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="input-box">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>

        <div class="link-text">
            <p>Don't have an account? <a href="signup.php">Signup Here</a></p>
            <p><a href="forgot_password.php">Forgot Password?</a></p>
        </div>
    </div>
</body>
</html>
