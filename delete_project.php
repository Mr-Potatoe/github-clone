<?php
// delete_project.php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$projectId = $_GET['id'];
$sql = "SELECT file_path FROM projects WHERE project_id='$projectId'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $file = $result->fetch_assoc()['file_path'];

    // Delete the file from the server
    if (unlink($file)) {
        // Delete project record from the database
        $sql = "DELETE FROM projects WHERE project_id='$projectId'";
        $conn->query($sql);
        echo "Project deleted successfully!";
    } else {
        echo "Error deleting project file.";
    }
} else {
    echo "Project not found.";
}
header("Location: admin.php");
?>
