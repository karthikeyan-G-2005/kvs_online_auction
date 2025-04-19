<?php
session_start();

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_code'])) {
    header("Location: forgot_password.php");
    exit;
}

$host = "localhost";
$dbname = "kvs_auction";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredCode = trim($_POST['reset_code']);
    $newPassword = trim($_POST['new_password']);

    if ($enteredCode == $_SESSION['reset_code']) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $email = $_SESSION['reset_email'];

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);

        if ($stmt->execute()) {
            session_unset();
            session_destroy();
            echo "<script>alert('Password updated successfully!'); window.location.href='login.php';</script>";
            exit;
        } else {
            echo "<script>alert('Failed to update password.');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Invalid reset code.');</script>";
    }
}

$conn->close();
?>

<!-- HTML FORM -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #89f7fe, #66a6ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .reset-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            width: 350px;
        }

        .reset-box h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        .reset-box label {
            display: block;
            margin-bottom: 6px;
        }

        .reset-box input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .reset-box button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
        }

        .reset-box button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="reset-box">
        <h2>Reset Password</h2>
        <form method="POST">
            <label for="reset_code">Reset Code</label>
            <input type="text" name="reset_code" id="reset_code" required>

            <label for="new_password">New Password</label>
            <input type="password" name="new_password" id="new_password" required>

            <button type="submit">Update Password</button>
        </form>
    </div>
</body>
</html>
