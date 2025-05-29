<?php
session_start();
include("db.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] === "staff" || $_SESSION["status"] !== "approved") {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Staff Report History</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
<h2>View Staff Report History</h2>

<form method="GET">
    <label>Select Staff:</label>
    <select name="staff_id" required>
        <option value="">-- Select Staff --</option>
        <?php
        $result = $conn->query("SELECT id, name FROM users WHERE role = 'staff' AND status = 'approved'");
        while ($row = $result->fetch_assoc()) {
            $selected = (isset($_GET["staff_id"]) && $_GET["staff_id"] == $row["id"]) ? "selected" : "";
            echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
        }
        ?>
    </select>
    <button type="submit">View</button>
</form>

<?php if (isset($_GET["staff_id"])): 
    $staff_id = $_GET["staff_id"];

    $stmt = $conn->prepare("SELECT reports.id, reports.report_date, report_tasks.task_content, report_tasks.self_grade, report_tasks.self_score FROM reports JOIN report_tasks ON reports.id = report_tasks.report_id WHERE reports.user_id = ? ORDER BY reports.report_date DESC");
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $reports = [];
    while ($row = $result->fetch_assoc()) {
        $date = $row["report_date"];
        $reports[$date][] = $row;
    }
?>
<h3>Report Calendar</h3>
<input type="text" id="calendar" placeholder="Pick a date">

<div id="reportDetails"></div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
const reportData = <?php echo json_encode($reports); ?>;

flatpickr("#calendar", {
    onChange: function(selectedDates, dateStr) {
        const container = document.getElementById("reportDetails");
        container.innerHTML = "";

        if (reportData[dateStr]) {
            reportData[dateStr].forEach(report => {
                container.innerHTML += `
                    <div style="border:1px solid #ccc; padding:10px; margin:10px 0;">
                        <strong>Task:</strong> ${report.task_content}<br>
                        <strong>Self Grade:</strong> ${report.self_grade}<br>
                        <strong>Self Score:</strong> ${report.self_score}
                    </div>
                `;
            });
        } else {
            container.innerHTML = "<p>No report submitted for this day.</p>";
        }
    }
});

</script>

<?php endif; ?>
</body>

<a href="management_dashboard.php">Back to dashboard</a>

</html>
