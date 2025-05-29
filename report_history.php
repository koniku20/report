<?php
// report_history.php
session_start();
include("db.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "staff") {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$selected_date = $_GET['date'] ?? date("Y-m-d");

// Fetch tasks for selected date
$stmt = $conn->prepare("SELECT rt.task_content, rt.self_grade, rt.self_score, rt.manager_grade, rt.manager_score 
                        FROM reports r JOIN report_tasks rt ON r.id = rt.report_id 
                        WHERE r.user_id = ? AND r.report_date = ?");
$stmt->bind_param("is", $user_id, $selected_date);
$stmt->execute();
$tasks = $stmt->get_result();

// Quarterly average
$quarter_start = date("Y-m-01", strtotime("-2 months"));
$quarter_end = date("Y-m-t");
$stmt_avg = $conn->prepare("SELECT AVG(rt.self_score) AS avg_self, AVG(rt.manager_score) AS avg_mgr 
                            FROM reports r JOIN report_tasks rt ON r.id = rt.report_id 
                            WHERE r.user_id = ? AND r.report_date BETWEEN ? AND ?");
$stmt_avg->bind_param("iss", $user_id, $quarter_start, $quarter_end);
$stmt_avg->execute();
$avg_result = $stmt_avg->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Report History</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .calendar-container { max-width: 300px; margin-bottom: 20px; }
        .task { border: 1px solid #ccc; padding: 10px; margin: 5px 0; border-radius: 4px; }
        .summary { background: #f0f0f0; padding: 10px; margin-top: 20px; }
    </style>
    <!-- Flatpickr CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
<body>
    <h2>Report History</h2>
    <div class="calendar-container">
        <input type="text" id="calendar" value="<?= htmlspecialchars($selected_date) ?>">
    </div>

    <h3>Tasks on <?= htmlspecialchars($selected_date) ?></h3>
    <?php if ($tasks->num_rows > 0): ?>
        <?php while ($task = $tasks->fetch_assoc()): ?>
            <div class="task">
                <strong>Task:</strong> <?= htmlspecialchars($task["task_content"]) ?><br>
                <strong>Self Grade:</strong> <?= $task["self_grade"] ?> (<?= $task["self_score"] ?>)<br>
                <strong>Manager Grade:</strong> <?= $task["manager_grade"] ?? 'Pending' ?> 
                <?= $task["manager_score"] ? '(' . $task["manager_score"] . ')' : '' ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No report submitted on this date.</p>
    <?php endif; ?>

    <div class="summary">
        <h3>Quarterly Performance</h3>
        <p><strong>Self Score Average:</strong> <?= round($avg_result["avg_self"] ?? 0, 2) ?></p>
        <p><strong>Manager Score Average:</strong> <?= round($avg_result["avg_mgr"] ?? 0, 2) ?></p>
    </div>

    <script>
        flatpickr("#calendar", {
            defaultDate: "<?= $selected_date ?>",
            onChange: function(selectedDates, dateStr, instance) {
                window.location.href = "report_history.php?date=" + dateStr;
            }
        });
    </script>
</body>
</html>
