<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['project_file'])) {
    $userId = $_SESSION['user_id'];
    $projectName = $_POST['project_name'];
    $targetDir = "uploads/" . $userId . "/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $filePath = $targetDir . basename($_FILES["project_file"]["name"]);
    
    if (move_uploaded_file($_FILES["project_file"]["tmp_name"], $filePath)) {
        $sql = "INSERT INTO projects (user_id, project_name, file_path, upload_date) VALUES ('$userId', '$projectName', '$filePath', NOW())";
        $conn->query($sql);
        echo "Project uploaded successfully!";
    } else {
        echo "Error uploading project!";
    }
}
?>
