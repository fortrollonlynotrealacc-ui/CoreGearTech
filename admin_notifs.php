<?php
session_start();
include '../Project in WST/server/server.php'; // Adjust path if necessary

// Fetch all messages from the contact_us table
$sql = "SELECT id, name, status FROM contact_us ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Notifications</title>
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; }
        .page-title { text-align: center; font-size: 24px; margin: 20px; color: white; }
        .notification-table { width: 90%; margin: 20px auto; background-color: #343a40; color: #ffffff; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #444; text-align: center; }
        th { background-color: rgba(64, 135, 148, 0.7); }
        .btn-view { padding: 5px 10px; background-color: #1c475c; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn-view:hover { opacity: 0.8; }
        /* Highlight unread rows */
        .unread { background-color: #343a35; } /* Light gray for unread */
        /* Video Background Styling */
        .video-background { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; overflow: hidden; z-index: -1;}
        #bg-video {width: 100%;height: 100%;object-fit: cover;}
        .video-background::before {content: ''; position: absolute;top: 0;left: 0;width: 100%;height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 1));z-index: 1;}
        /* Return button */
        .btn-return { position: absolute; top: 5px;left: 20px; background-color: transparent; border: none; color: white;
            font-size: 2rem; cursor: pointer; transition: color 0.3s ease;}
            .btn-return:hover { color: #f0f0f0;  text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5); }
    </style>
</head>
<body>
    <div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <button class="btn-return" style="margin-top: 5%; color: white;" onclick="window.location.href='adminwebsite.php';">
        <i class="fa fa-reply"></i>
    </button>
    <h1 class="page-title">Admin Notifications</h1>

    <?php if ($result->num_rows > 0) : ?>
        <table class="notification-table">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr class="<?php echo ($row['status'] === 'unread') ? 'unread' : ''; ?>">
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                    <td>
                        <form action="view_message.php" method="get" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn-view">View</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else : ?>
        <p class="page-title">No messages found.</p>
    <?php endif; ?>
</body>
</html>
