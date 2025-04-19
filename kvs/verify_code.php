<?php
session_start();

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_POST['code'];

    if ($code == $_SESSION['reset_code']) {
        $_SESSION['code_verified'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Invalid verification code.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Code</title>
</head>
<body>
    <h2>Enter Verification Code</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="code" required>
        <button type="submit">Verify</button>
    </form>
</body>
</html>
