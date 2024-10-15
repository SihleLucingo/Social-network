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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profile_pic'])) {
    $user_id = $_SESSION['user_id'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if uploads directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
    if($check !== false) {
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $sql = "UPDATE users SET profile_pic='$target_file' WHERE id='$user_id'";
            if ($conn->query($sql) === TRUE) {
                echo "Profile picture uploaded successfully!";
            } else {
                echo "Error updating database: " . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "File is not an image.";
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
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="message.php">messages</a></li>
                    <li><a href="index.php">Home</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <form method="post" enctype="multipart/form-data">
            Select image to upload:
            <input type="file" name="profile_pic" required><br>
            <input type="submit" value="Upload Image">
        </form>
    </div>
</body>
</html>
