<?php
session_start();
$servername = "localhost"; 
$username = "root";
$password = "";
$dbname = "402307245";

$db = mysqli_connect($servername, $username, $password, $dbname);

// Check database connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Checking if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get user ID from session

// Handling post creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_content'])) {
    $content = mysqli_real_escape_string($db, trim($_POST['post_content'])); // Trim whitespace from input
    if (!empty($content)) { // Check if content is not empty
        $sql = "INSERT INTO posts (user_id, content) VALUES ('$user_id', '$content')";
        if (!mysqli_query($db, $sql)) {
            echo "Error: " . mysqli_error($db); // Display error if query fails
        }
    } else {
        echo "<p style='color: red;'>Post content cannot be empty.</p>"; // User feedback for empty post
    }
}

// Handling user's search
$search_results = [];
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search_term = mysqli_real_escape_string($db, $_GET['search']);
    $sql = "SELECT id, username, email FROM users WHERE username LIKE '%$search_term%' OR email LIKE '%$search_term%'";
    $result = mysqli_query($db, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $search_results[] = $row;
        }
    } else {
        echo "Error fetching search results: " . mysqli_error($db); // Display error if query fails
    }
}

// Getting user's posts
$posts = [];
$sql = "SELECT content, created_at FROM posts WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($db, $sql);
if (!$result) {
    echo "Error fetching posts: " . mysqli_error($db); // Display error if query fails
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = $row; // Add each post to the array
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Network</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head> 
<body>
    <div class="container">
        <header>
            <div class="container">
                <div id="branding">
                    <h1><i>402307245 Social</i> Network</h1>
                </div>
                <nav>
                    <ul>
                        <li><a href="message.php">Messages</a></li>
                        <li><a href="profile.php">Profile</a></li>
                        <li><a href="index.php">Home</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1> <!-- Ensure username is set in session -->
        <img src="richfield_logo.png" alt="Richfield Logo">
        
        <a href="upload_profile_pic.php">Upload New Profile Picture</a>
        
        <h2>Create a Post</h2>
        <form method="post" action="">
            <textarea name="post_content" placeholder="Share something..." rows="4" style="width: 100%;"></textarea>
            <button type="submit" class="button">Post</button>
        </form>
        
        <h2>Your Posts</h2>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <p><?php echo htmlspecialchars($post['content']); ?></p>
                <small><?php echo htmlspecialchars($post['created_at']); ?></small> <!-- Escape output for security -->
            </div>
        <?php endforeach; ?>
        
        <h2>Search Users</h2>
        <form method="get" action="">
            <input type="text" name="search" class="search-box" placeholder="Search by name or email">
            <button type="submit" class="button">Search</button>
        </form>
        
        <h2>Search Results</h2>
        <?php foreach ($search_results as $result): ?>
            <div class="search-result">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($result['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($result['email']); ?></p>
                <a href="profile.php?id=<?php echo $result['id']; ?>">View Profile</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>