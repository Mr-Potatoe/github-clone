<?php
// student.php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['project_file'])) {
    $projectName = $_POST['project_name'];
    $targetDir = "uploads/" . $userId . "/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $filePath = $targetDir . basename($_FILES["project_file"]["name"]);
    $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    if ($fileType === 'zip') {
        if (move_uploaded_file($_FILES["project_file"]["tmp_name"], $filePath)) {
            // Save project info in the database
            $sql = "INSERT INTO projects (user_id, project_name, file_path, upload_date) VALUES ('$userId', '$projectName', '$filePath', NOW())";
            $conn->query($sql);
            echo "Project uploaded successfully!";
        } else {
            echo "Error uploading project!";
        }
    } else {
        echo "Only ZIP files are allowed.";
    }
}

// Retrieve student's uploaded projects
$sql = "SELECT * FROM projects WHERE user_id='$userId'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="student.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
        
        <!-- Upload Project Form -->
        <h3>Upload New Project</h3>
        <form action="student.php" method="post" enctype="multipart/form-data">
            <label for="project_name">Project Name:</label>
            <input type="text" name="project_name" required>
            <label for="project_file">Upload Project (ZIP only):</label>
            <input type="file" name="project_file" accept=".zip" required>
            <button type="submit">Upload Project</button>
        </form>

        <!-- Display Projects as Cards -->
        <h3>Your Projects</h3>
        <div class="project-cards-container">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="project-card">
                    <div class="project-card-header">
                        <h4><?php echo $row['project_name']; ?></h4>
                        <p><small>Uploaded on: <?php echo $row['upload_date']; ?></small></p>
                    </div>
                    <div class="project-card-body">
                        <a href="<?php echo $row['file_path']; ?>" download class="download-link">Download</a>
                    </div>
                </div>
            <?php } ?>
        </div>

        <a href="logout.php" class="logout">Logout</a>
    </div>
</body>
</html>
