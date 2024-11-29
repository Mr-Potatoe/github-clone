<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectId = $_POST['project_id'] ?? null;

    if ($projectId) {
        // Delete project members
        $conn->query("DELETE FROM project_members WHERE project_id = $projectId");

        // Delete project
        $stmt = $conn->prepare("DELETE FROM projects WHERE project_id = ?");
        $stmt->bind_param("i", $projectId);

        if ($stmt->execute()) {
            $_SESSION['delete_status'] = "Project deleted successfully.";
        } else {
            $_SESSION['delete_status'] = "Error deleting project: " . $stmt->error;
        }
    }
    header("Location: projects.php");
    exit;
}
?>
