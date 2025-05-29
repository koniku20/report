<?php
// management_dashboard.php
session_start();
include("db.php");

if (!isset($_SESSION["user_id"]) || !in_array($_SESSION["role"], ['supervisor', 'director', 'ceo'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Management Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION["name"]; ?>!</h2>

    <nav>
        <ul>
            <li><a href="pending_approvals.php">Pending User Approvals</a></li>
            <li><a href="view_reports.php">View Reports</a></li>
            <li><a href="performance_summary.php">Performance Summary</a></li>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="all_report_history.php">history</a></li>

        </ul>
    </nav>

    <section>
        <h3>Dashboard Features</h3>
        <p>From here, you can:</p>
        <ul>
            <li>Approve or reject new user registrations.</li>
            <li>View submitted reports and leave comments/grades.</li>
            <li>View quarterly performance summaries of staff.</li>
        </ul>
    </section>
</body>
</html>
