<?php
// reset_password.php
include("db.php");

if (isset($_GET["token"])) {
    $token = $_GET["token"];

    $stmt = $conn->prepare("SELECT email, token_expiry FROM users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && strtotime($user["token_expiry"]) > time()) {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $new_password = password_hash($_POST["password"], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?");
            $stmt->bind_param("ss", $new_password, $token);
            $stmt->execute();
            echo "Password has been reset. <a href='index.php'>Login</a>";
            exit();
        }
    } else {
        echo "Invalid or expired token.";
        exit();
    }
} else {
    echo "No reset token provided.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head><title>Reset Password</title></head>
<body>
<h2>Reset Your Password</h2>
<form method="POST">
    <label>New Password:</label><br>
    <input type="password" name="password" required><br><br>
    <button type="submit">Reset Password</button>
</form>
</body>
</html>
