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

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$user_id = mysqli_real_escape_string($conn, $_GET['id']);

$sql = "SELECT username, email, profile_pic FROM users WHERE id='$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="upload_profile_pic.php">Upload Profile Picture</a></li>
                    <li><a href="message.php">Messages</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <h2><?php echo htmlspecialchars($user['username']); ?>'s Profile</h2>
        <?php if ($user['profile_pic']): ?>
            <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%;">
        <?php else: ?>
            <p>No profile picture uploaded.</p>
        <?php endif; ?>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    </div>
</body>
</html>
