<?php
session_start();
include("db.php");


// Check if user is logged in, role = staff, and status = approved
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"]) || !isset($_SESSION["status"]) 
    || $_SESSION["role"] !== "staff" || $_SESSION["status"] !== "approved") {
    header("Location: index.php?error=unauthorized");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Welcome, <?= htmlspecialchars($_SESSION["name"]) ?> (<?= htmlspecialchars($_SESSION["department"]) ?>)</h2>

    <ul>
        <li><a href="submit_report.php">Submit Report</a></li>
        <li><a href="report_history.php">Report History</a></li>
        <li><a href="logout.php">Logout</a></li>
        <li><a href="performance_summary.php">performance</a></li>
        
    </ul>
</body>
</html>
