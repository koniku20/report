<?php
// fetch_report_by_date.php
include("db.php");

$user_id = $_GET["user_id"] ?? "";
$date = $_GET["date"] ?? "";

if (!$user_id || !$date) {
    echo "Invalid request.";
    exit();
}

$stmt = $conn->prepare("SELECT r.id AS report_id FROM reports r WHERE r.user_id = ? AND r.report_date = ?");
$stmt->bind_param("is", $user_id, $date);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "No report submitted for this date.";
    exit();
}

$row = $res->fetch_assoc();
$report_id = $row["report_id"];

$stmt = $conn->prepare("SELECT task_content, self_grade, self_score, manager_grade, manager_comment FROM report_tasks WHERE report_id = ?");
$stmt->bind_param("i", $report_id);
$stmt->execute();
$res = $stmt->get_result();

echo "<h3>Report for " . htmlspecialchars($date) . "</h3>";
while ($task = $res->fetch_assoc()) {
    echo "<div style='margin-bottom:10px;'>";
    echo "<strong>Task:</strong> " . nl2br(htmlspecialchars($task["task_content"])) . "<br>";
    echo "<strong>Self Grade:</strong> " . $task["self_grade"] . " (" . $task["self_score"] . ")<br>";
    echo "<strong>Manager Grade:</strong> " . ($task["manager_grade"] ?? "N/A") . "<br>";
    echo "<strong>Manager Comment:</strong><br>" . nl2br($task["manager_comment"] ?? "No comment") . "<br>";
    echo "</div><hr>";
}
