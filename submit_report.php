<?php
// submit_report.php
session_start();
include("db.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "staff") {
    echo "Access denied.";
    exit();
}

$user_id = $_SESSION["user_id"];
$date_today = date("Y-m-d");

$grade_scores = ["A" => 90, "B" => 80, "C" => 50, "D" => 49, "E" => 45, "F" => 39];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tasks = $_POST["tasks"] ?? [];
    $grades = $_POST["grades"] ?? [];

    if (empty($tasks) || empty($grades)) {
        echo "Please enter at least one task and grade.";
        exit();
    }

    // Prevent multiple submissions per day
    $stmt = $conn->prepare("SELECT id FROM reports WHERE user_id = ? AND report_date = ?");
    $stmt->bind_param("is", $user_id, $date_today);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "You have already submitted a report today.";
        exit();
    }

    // Insert report
    $stmt = $conn->prepare("INSERT INTO reports (user_id, report_date) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $date_today);
    $stmt->execute();
    $report_id = $conn->insert_id;

    // Insert each task with grade and score
    $stmt_task = $conn->prepare("INSERT INTO report_tasks (report_id, task_content, self_grade, self_score) VALUES (?, ?, ?, ?)");
    foreach ($tasks as $i => $task) {
        $task = trim($task);
        $grade = strtoupper(trim($grades[$i] ?? ""));
        if ($task === "" || !isset($grade_scores[$grade])) {
            continue;
        }
        $score = $grade_scores[$grade];
        $stmt_task->bind_param("isss", $report_id, $task, $grade, $score);
        $stmt_task->execute();
    }

    echo "Report submitted successfully. <a href='staff_dashboard.php'>Go back</a>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Daily Report</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function addTaskField() {
            const container = document.getElementById('tasks-container');
            const index = container.children.length;
            const div = document.createElement('div');
            div.innerHTML = `
                <textarea name="tasks[]" rows="3" cols="50" placeholder="Enter task ${index + 1}" required></textarea>
                <select name="grades[]" required>
                    <option value="">Grade</option>
                    <option value="A">A (90)</option>
                    <option value="B">B (80)</option>
                    <option value="C">C (50)</option>
                    <option value="D">D (49)</option>
                    <option value="E">E (45)</option>
                    <option value="F">F (39)</option>
                </select>
                <br><br>
            `;
            container.appendChild(div);
        }
    </script>
</head>
<body>
    <h2>Submit Daily Report (<?= date("Y-m-d") ?>)</h2>
    <form method="POST" action="">
        <div id="tasks-container">
            <div>
                <textarea name="tasks[]" rows="3" cols="50" placeholder="Enter task 1"></textarea>
                <select name="grades[]" required>
                    <option value="">Grade</option>
                    <option value="A">A (90)</option>
                    <option value="B">B (80)</option>
                    <option value="C">C (50)</option>
                    <option value="D">D (49)</option>
                    <option value="E">E (45)</option>
                    <option value="F">F (39)</option>
                </select>
                <br><br>
            </div>
        </div>
        <button type="button" onclick="addTaskField()">Add Another Task</button><br><br>
        <button type="submit">Submit Report</button>
    </form>
    <p><a href="staff_dashboard.php">Back to Dashboard</a></p>
</body>
</html>
