<?php
include '../Project in WST/server/server.php';

// Get the user ID from the URL
if (!isset($_GET['user_id'])) {
    die("No user specified.");
}
$user_id = intval($_GET['user_id']);

// Fetch the user's name (fallback to "User [ID]" if no account is found)
$query = "SELECT CONCAT(fname, ' ', lname, ' ', mname) AS full_name 
          FROM cgt_accounts 
          WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($full_name);
$stmt->fetch();
$stmt->close();

if (!$full_name) {
    $full_name = "User [ID: $user_id]";
}

// Fetch all messages between the admin and the user
$messages_query = "SELECT message, message_type, created_at 
                   FROM chat_messages 
                   WHERE user_id = ? 
                   ORDER BY created_at ASC";
$msg_stmt = $conn->prepare($messages_query);
$msg_stmt->bind_param('i', $user_id);
$msg_stmt->execute();
$result = $msg_stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$msg_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <title>Messages with <?php echo htmlspecialchars($full_name); ?></title>
    <style>
        /* Styling for body, video background, and return button */
        body {
            background-color: #1a1a1a;
            color: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        h1 {
            padding-top: 50px;
            text-align: center;
            margin: 20px 0;
        }
        #messages {
            max-height: 400px;
            max-width: 900px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #333;
            border-radius: 5px;
            margin-left: 20%;
        }
        #messages div {
            margin-bottom: 10px;
            color: #ddd;
        }
        textarea {
            width: 100%;
            height: 50px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            background-color: #222;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
        }
        .b1 {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .b1:hover {
            background-color: #0056b3;
        }
        form {
            width: 80%;
            margin: 0 auto;
            max-width: 600px;
        }
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            z-index: -1;
        }
        #bg-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .video-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 1));
            z-index: 1;
        }
        .btn-return {
            position: absolute;
            top: 5px;
            left: 20px;
            background-color: transparent;
            border: none;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .btn-return:hover {
            color: #f0f0f0;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <button class="btn-return" onclick="window.location.href='view_user_messages.php';">
        <i class="fa fa-reply"></i>
    </button>
    <h1>Messages with <?php echo htmlspecialchars($full_name); ?></h1>
    <div id="messages"></div>
    <form id="reply-form">
        <textarea name="message" placeholder="Reply to user..." required></textarea>
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="hidden" name="message_type" value="admin">
        <button type="submit" class="b1" id="send-btn">Send</button>
    </form>

    <script>
        // Fetch messages from the server
        async function fetchMessages() {
            try {
                const response = await fetch('admin_get_messages.php?user_id=<?php echo $user_id; ?>');
                const messages = await response.json();
                const messagesDiv = document.getElementById('messages');

                // Clear existing messages
                messagesDiv.innerHTML = messages.map(msg => `
                    <div><strong>${msg.message_type.charAt(0).toUpperCase() + msg.message_type.slice(1)}:</strong> 
                    ${msg.message} <em>(${msg.created_at})</em></div>
                `).join('');

                // Scroll to the bottom
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            } catch (error) {
                console.error('Error fetching messages:', error);
            }
        }

        // Submit form via AJAX
        document.getElementById('reply-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const sendButton = document.getElementById('send-btn');
            sendButton.disabled = true; // Disable button during submission

            try {
                const formData = new FormData(form);
                const response = await fetch('send_message.php', {
                    method: 'POST',
                    body: formData,
                });
                const result = await response.json();

                if (result.success) {
                    form.reset();
                    fetchMessages(); // Reload messages
                } else {
                    alert(result.error || 'Failed to send the message.');
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('An error occurred while sending the message.');
            } finally {
                sendButton.disabled = false; // Re-enable the button
            }
        });

        // Initial fetch and periodic refresh
        fetchMessages();
        setInterval(fetchMessages, 5000);
    </script>
</body>
</html>
