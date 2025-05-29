<?php
// pending_approvals.php
session_start();
include("db.php");

if (!isset($_SESSION["user_id"]) || !in_array($_SESSION["role"], ['supervisor', 'director', 'ceo'])) {
    header("Location: index.php");
    exit();
}

// Handle approval or rejection
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["user_id"], $_POST["action"])) {
    $user_id = intval($_POST["user_id"]);
    $action = $_POST["action"];

    if ($action === "approve") {
        $stmt = $conn->prepare("UPDATE users SET status = 'approved' WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    } elseif ($action === "reject") {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }

    header("Location: pending_approvals.php");
    exit();
}

// Fetch pending users
$result = $conn->query("SELECT id, name, email, department, gender FROM users WHERE status = 'pending'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pending Approvals</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Pending User Approvals</h2>
    <a href="management_dashboard.php">← Back to Dashboard</a>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Gender</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row["name"]) ?></td>
                    <td><?= htmlspecialchars($row["email"]) ?></td>
                    <td><?= htmlspecialchars($row["department"]) ?></td>
                    <td><?= htmlspecialchars($row["gender"]) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="action" value="approve">Approve</button>
                            <button type="submit" name="action" value="reject">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No users pending approval.</p>
    <?php endif; ?>
</body>
</html>
