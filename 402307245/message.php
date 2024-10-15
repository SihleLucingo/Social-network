<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "402307245";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle sending messages
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['receiver_id']) && isset($_POST['message'])) {
    $receiver_id = mysqli_real_escape_string($conn, $_POST['receiver_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Check if receiver exists
    $check_receiver = "SELECT id FROM users WHERE id='$receiver_id'";
    $receiver_result = $conn->query($check_receiver);

    if ($receiver_result->num_rows > 0) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES ('$user_id', '$receiver_id', '$message')";
        if ($conn->query($sql) === TRUE) {
            echo "Message sent!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Receiver ID does not exist.";
    }
}

// Fetching messages
$messages = [];
$sql = "SELECT messages.message, users.username, messages.created_at 
        FROM messages 
        JOIN users ON messages.sender_id = users.id 
        WHERE messages.receiver_id='$user_id' 
        ORDER BY messages.created_at DESC";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1>402307245 Social Network</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="index.php">Home</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <h2>Send a Message</h2>
        <form method="post" action="">
            <label for="receiver_id">Receiver ID:</label>
            <input type="text" name="receiver_id" id="receiver_id" required><br>
            <label for="message">Message:</label>
            <textarea name="message" id="message" required></textarea><br>
            <input type="submit" value="Send Message">
        </form>

        <h2>Your Messages</h2>
        <?php if (count($messages) > 0): ?>
            <?php foreach ($messages as $message): ?>
                <div class="message">
                    <p><strong><?php echo htmlspecialchars($message['username']); ?>:</strong> <?php echo htmlspecialchars($message['message']); ?></p>
                    <small><?php echo $message['created_at']; ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No messages yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
