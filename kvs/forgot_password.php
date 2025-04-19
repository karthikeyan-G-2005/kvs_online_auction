<?php
session_start();

$host = "localhost";
$dbname = "kvs_auction";
$username = "root";
$password = "";
$conn = new mysqli($host, $username, $password, $dbname);

$message = "";
$code = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $code = rand(100000, 999999); // Simulate reset code
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_code'] = $code;

        $message = "<p class='success'>
                        Your reset code is: <strong>$code</strong><br>(Simulated email)<br><br>
                        <a href='reset_password.php' style='color: #007bff;'>Click here to reset your password</a>
                    </p>";
    } else {
        $message = "<p class='error'>Email not found. Please try again.</p>";
    }

    $stmt->close();
}
$conn->close();
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .box {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 350px;
            text-align: center;
        }
        .box h2 {
            margin-bottom: 20px;
        }
        input[type="email"] {
            padding: 10px;
            width: 90%;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .success {
            color: green;
            margin-top: 20px;
        }
        .error {
            color: red;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="box">
    <h2>Forgot Password</h2>
    <form method="POST" action="forgot_password.php">
        <input type="email" name="email" placeholder="Enter your registered email" required><br>
        <button type="submit">Get Reset Code</button>
    </form>
    <?php if (!empty($message)) echo $message; ?>
</div>
</body>
</html>
