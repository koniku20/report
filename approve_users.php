<?php
session_start();
include("db.php");

if (!isset($_SESSION["user_id"]) || !in_array($_SESSION["user_role"], ["supervisor", "director", "ceo"])) {
    echo "Access denied.";
    exit();
}

// Approve user
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["approve_id"])) {
    $user_id = $_POST["approve_id"];
    $stmt = $conn->prepare("UPDATE users SET status = 'approved' WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    echo "User approved successfully.<br><br>";
}

// Change role
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["change_role_id"])) {
    $user_id = $_POST["change_role_id"];
    $new_role = $_POST["new_role"];
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $user_id);
    $stmt->execute();
    echo "Role updated successfully.<br><br>";
}

// Get all pending users
$result = $conn->query("SELECT id, name, email, department, gender, role FROM users WHERE status = 'pending'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Users</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Pending User Approvals</h2>

<?php if ($result->num_rows > 0): ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Name</th><th>Email</th><th>Department</th><th>Gender</th><th>Role</th><th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row["name"]) ?></td>
            <td><?= htmlspecialchars($row["email"]) ?></td>
            <td><?= htmlspecialchars($row["department"]) ?></td>
            <td><?= htmlspecialchars($row["gender"]) ?></td>
            <td><?= htmlspecialchars($row["role"]) ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="approve_id" value="<?= $row["id"] ?>">
                    <button type="submit">Approve</button>
                </form>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="change_role_id" value="<?= $row["id"] ?>">
                    <select name="new_role">
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                        <option value="corp-member">Corp-member</option>
                        <option value="intern">Intern</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="director">Director</option>
                        <option value="ceo">CEO</option>
                    </select>
                    <button type="submit">Change Role</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No users pending approval.</p>
<?php endif; ?>

<p><a href="management_dashboard.php">Back to Dashboard</a></p>
</body>
</html>
