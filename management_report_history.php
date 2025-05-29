<?php
// management_report_history.php
session_start();
include("db.php");

if (!isset($_SESSION["user_id"]) || !in_array($_SESSION["role"], ["supervisor", "director", "ceo"])) {
    header("Location: index.php");
    exit();
}

$departments = ["engineering", "ICT", "program", "account", "news", "corp-member", "intern", "admin", "marketing"];

$selected_department = $_GET["department"] ?? "";
$start_date = $_GET["start_date"] ?? "";
$end_date = $_GET["end_date"] ?? "";

$where_clauses = [];
$params = [];
$types = "";

if ($selected_department && in_array($selected_department, $departments)) {
    $where_clauses[] = "u.department = ?";
    $params[] = $selected_department;
    $types .= "s";
}

if ($start_date) {
    $where_clauses[] = "r.report_date >= ?";
    $params[] = $start_date;
    $types .= "s";
}

if ($end_date) {
    $where_clauses[] = "r.report_date <= ?";
    $params[] = $end_date;
    $types .= "s";
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

$sql = "SELECT r.id, r.report_date, u.username, u.department
        FROM reports r
        JOIN users u ON r.user_id = u.id
        $where_sql
        ORDER BY r.report_date DESC";

$stmt = $conn->prepare($sql);

if ($where_sql) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Management Report History</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Management Report History</h2>

<form method="GET" action="">
    <label>Department: </label>
    <select name="department">
        <option value="">-- All Departments --</option>
        <?php foreach ($departments as $dept): ?>
            <option value="<?= $dept ?>" <?= $dept === $selected_department ? "selected" : "" ?>><?= ucfirst($dept) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Start Date: </label>
    <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">

    <label>End Date: </label>
    <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">

    <button type="submit">Filter</button>
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
                    <td><?= htmlspecialchars($report["username"]) ?></td>
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
