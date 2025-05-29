<?php
// view_reports.php
session_start();
include("db.php");

if (!isset($_SESSION["user_id"]) || !in_array($_SESSION["role"], ["supervisor", "director", "ceo"])) {
    header("Location: index.php");
    exit();
}

$departments = ["engineering", "ICT", "program", "account", "news", "corp-member", "intern", "admin", "marketing"];
$selected_department = $_GET["department"] ?? "";

$where_clause = "";
$params = [];
$types = "";

if ($selected_department && in_array($selected_department, $departments)) {
    $where_clause = "WHERE u.department = ?";
    $params[] = $selected_department;
    $types .= "s";
}

// Fetch reports with user info, ordered by date desc
$sql = "SELECT r.id, r.user_id, u.name, u.department, r.report_date 
        FROM reports r
        JOIN users u ON r.user_id = u.id
        $where_clause
        ORDER BY r.report_date DESC";

$stmt = $conn->prepare($sql);
if ($where_clause) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Staff Reports</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Staff Reports</h2>

<form method="GET" action="">
    <label>Filter by Department: </label>
    <select name="department" onchange="this.form.submit()">
        <option value="">-- All Departments --</option>
        <?php foreach ($departments as $dept): ?>
            <option value="<?= $dept ?>" <?= $dept === $selected_department ? "selected" : "" ?>><?= ucfirst($dept) ?></option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($result->num_rows === 0): ?>
    <p>No reports found.</p>
<?php else: ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Username</th>
                <th>Department</th>
                <th>Report Date</th>
                <th>View Report</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($report = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($report["name"]) ?></td>
                    <td><?= htmlspecialchars($report["department"]) ?></td>
                    <td><?= htmlspecialchars($report["report_date"]) ?></td>
                    <td><a href="view_report.php?report_id=<?= $report["id"] ?>">View / Comment</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

<p><a href="management_dashboard.php">Back to Dashboard</a></p>
</body>
</html>
