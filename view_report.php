<?php
// view_reports.php
session_start();
include("db.php");

if (!isset($_SESSION["user_id"]) || !in_array($_SESSION["role"], ['supervisor', 'director', 'ceo'])) {
    header("Location: index.php");
    exit();
}

$grade_scores = ["A" => 90, "B" => 80, "C" => 50, "D" => 49, "E" => 45, "F" => 39];

// Handle comment and grade submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["task_id"])) {
    $task_id = $_POST["task_id"];
    $manager_grade = $_POST["manager_grade"];
    $manager_comment = $_POST["manager_comment"];
    $score = $grade_scores[$manager_grade];

    $stmt = $conn->prepare("UPDATE report_tasks SET manager_grade = ?, manager_score = ?, manager_comment = ? WHERE id = ?");
    $stmt->bind_param("sisi", $manager_grade, $score, $manager_comment, $task_id);
    $stmt->execute();
}

// Get reports
$reports = $conn->query("
    SELECT r.id AS report_id, r.report_date, u.name, u.department, rt.id AS task_id, rt.task_content, rt.self_grade, rt.manager_grade, rt.manager_comment 
    FROM reports r
    JOIN users u ON r.user_id = u.id
    JOIN report_tasks rt ON r.id = rt.report_id
    ORDER BY r.report_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Reports</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Submitted Reports</h2>
    <a href="management_dashboard.php">← Back to Dashboard</a>

    <?php if ($reports->num_rows > 0): ?>
        <table>
            <tr>
                <th>Date</th>
                <th>Staff</th>
                <th>Department</th>
                <th>Task</th>
                <th>Self Grade</th>
                <th>Manager Grade</th>
                <th>Comment</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $reports->fetch_assoc()): ?>
                <tr>
                    <form method="POST">
                        <td><?= $row['report_date'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['department']) ?></td>
                        <td><?= htmlspecialchars($row['task_content']) ?></td>
                        <td><?= $row['self_grade'] ?></td>
                        <td>
                            <select name="manager_grade" required>
                                <option value="">--Grade--</option>
                                <?php foreach ($grade_scores as $grade => $score): ?>
                                    <option value="<?= $grade ?>" <?= $row['manager_grade'] === $grade ? 'selected' : '' ?>><?= $grade ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <textarea name="manager_comment" rows="2" cols="20"><?= htmlspecialchars($row['manager_comment']) ?></textarea>
                        </td>
                        <td>
                            <input type="hidden" name="task_id" value="<?= $row['task_id'] ?>">
                            <button type="submit">Submit</button>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No reports submitted yet.</p>
    <?php endif; ?>
</body>
</html>
