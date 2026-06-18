<?php
include '../Project in WST/server/server.php';

// Get the latest message for each user
$sql = "
    SELECT 
        users.id AS user_id,
        users.name AS user_name,
        MAX(messages.sent_at) AS latest_message_time,
        MAX(messages.message) AS latest_message,
        SUM(CASE WHEN messages.is_read = 0 AND messages.receiver_id = 0 THEN 1 ELSE 0 END) AS unread_count
    FROM users
    LEFT JOIN messages ON users.id = messages.sender_id
    GROUP BY users.id
    ORDER BY latest_message_time DESC
";

$result = $conn->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$conn->close();

echo json_encode($users);
?>
<div id="admin-message-list"></div>

<script>
function fetchMessages() {
    fetch('') // Fetch all users with messages
        .then(response => response.json())
        .then(users => {
            const messageList = document.getElementById('admin-message-list');
            messageList.innerHTML = ''; // Clear existing content

            users.forEach(user => {
                const userItem = document.createElement('div');
                userItem.className = 'user-item';
                userItem.innerHTML = `
                    <strong>${user.user_name}</strong> 
                    <span>${user.unread_count > 0 ? 'Unread' : 'Read'}</span>
                    <p>${user.latest_message}</p>
                    <small>${user.latest_message_time}</small>
                    <button onclick="viewMessages(${user.user_id})">View</button>
                `;
                messageList.appendChild(userItem);
            });
        });
}

function viewMessages(userId) {
    window.location.href = `view_message.php?user_id=${userId}`; // Redirect to view messages
}

// Fetch messages on page load
fetchMessages();
</script>
