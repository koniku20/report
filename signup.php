<?php
// signup.php
include("db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $department = $_POST["department"];
    $gender = $_POST["gender"];

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "Email already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, department, gender, role, status) VALUES (?, ?, ?, ?, ?, 'staff', 'pending')");
        $stmt->bind_param("sssss", $name, $email, $password, $department, $gender);
        if ($stmt->execute()) {
            $message = "Signup successful! Awaiting approval. <a href='index.php'>Login</a>";
        } else {
            $message = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Signup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>User Signup</h2>
    <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>
    <form method="POST" action="">
        <label>Full Name</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Department</label>
        <select name="department" required>
            <option value="">--Select--</option>
            <option value="Engineering">Engineering</option>
            <option value="ICT">ICT</option>
            <option value="Program">Program</option>
            <option value="Account">Account</option>
            <option value="News">News</option>
            <option value="Corp-member">Corp-member</option>
            <option value="Intern">Intern</option>
            <option value="Admin">Admin</option>
            <option value="Marketing">Marketing</option>
        </select>

        <label>Gender</label>
        <select name="gender" required>
            <option value="">--Select--</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <button type="submit">Sign Up</button>
    </form>
</div>
</body>
</html>
