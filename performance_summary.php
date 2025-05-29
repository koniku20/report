<?php
session_start();
include("db.php");

if (!isset($_SESSION["user_id"]) || !in_array($_SESSION["role"], ["supervisor", "director", "ceo"])) {
    header("Location: index.php");
    exit();
}

// Fetch all approved staff
$result = $conn->query("SELECT id, name FROM users WHERE status = 'approved' AND role = 'staff'");
$staff_list = [];
while ($row = $result->fetch_assoc()) {
    $staff_list[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quarterly Performance Summary</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Quarterly Performance Summary</h2>
<a href="management_dashboard.php">Back to Dashboard</a> | 
<a href="logout.php">Logout</a>
<hr>

<form method="GET" action="">
    <label>Select Staff:</label>
    <select name="staff_id" required>
        <option value="">--Select Staff--</option>
        <?php foreach ($staff_list as $staff): ?>
            <option value="<?= $staff["id"] ?>" <?= isset($_GET["staff_id"]) && $_GET["staff_id"] == $staff["id"] ? "selected" : "" ?>>
                <?= htmlspecialchars($staff["name"]) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">View Summary</button>
</form>

<?php
if (isset($_GET["staff_id"])) {
    $staff_id = intval($_GET["staff_id"]);

    $stmt = $conn->prepare("
        SELECT 
            YEAR(report_date) as year,
            QUARTER(report_date) as quarter,
            ROUND(AVG(self_score), 2) as avg_self,
            ROUND(AVG(manager_score), 2) as avg_manager
        FROM reports 
        JOIN report_tasks ON reports.id = report_tasks.report_id
        WHERE reports.user_id = ?
        GROUP BY year, quarter
        ORDER BY year DESC, quarter DESC
    ");
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0): ?>
        <h3>Performance Summary</h3>
        <table border="1" cellpadding="8">
            <tr>
                <th>Year</th>
                <th>Quarter</th>
                <th>Average Self Score</th>
                <th>Average Manager Score</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row["year"] ?></td>
                <td>Q<?= $row["quarter"] ?></td>
                <td><?= $row["avg_self"] ?></td>
                <td><?= $row["avg_manager"] ?? 'N/A' ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No reports available for this staff.</p>
    <?php endif;
}
?>
</body>
</html>
