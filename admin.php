<?php
// admin.php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Retrieve all uploaded projects
$sql = "SELECT projects.project_id, projects.project_name, projects.upload_date, projects.file_path, users.username 
        FROM projects 
        INNER JOIN users ON projects.user_id = users.user_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <!-- Parallax Background Layers -->
    <div class="parallax-layer" data-speed="0.2"></div>
    <div class="parallax-layer" data-speed="0.5"></div>
    <div class="parallax-layer" data-speed="0.8"></div>

    <!-- Admin Content -->
    <div class="content">
        <h2>Welcome, <?php echo $_SESSION['username']; ?> (Admin)</h2>
        <h3>All Student Projects</h3>

        <!-- Table for displaying project information -->
        <table border="1">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Uploaded by</th>
                    <th>Upload Date</th>
                    <th>File</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['project_name']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['upload_date']; ?></td>
                        <td><a href="<?php echo $row['file_path']; ?>" download>Download</a></td>
                        <td>
                            <a href="delete_project.php?id=<?php echo $row['project_id']; ?>" onclick="return confirm('Are you sure you want to delete this project?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <script src="admin.js"></script>
</body>
</html>
