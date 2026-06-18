<?php
session_start();
include '../Project in WST/server/server.php';

// Fetch users who have messaged
$query = "SELECT DISTINCT user_id FROM messages ORDER BY created_at DESC";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User Messages</title>
</head>
<body>
    <h1>User Messages</h1>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php
            $user_id = $row['user_id'];
            $user_query = $conn->query("SELECT name FROM users WHERE id = $user_id");
            $user = $user_query->fetch_assoc();
            ?>
            <li>
                <a href="admin_chat.php?user_id=<?= $user_id ?>">
                    <?= htmlspecialchars($user['name']) ?>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
