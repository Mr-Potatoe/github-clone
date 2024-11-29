<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$successMessage = "";

// Handle form submission to add a new project
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectName = $_POST['project_name'] ?? '';
    $projectMembers = $_POST['project_members'] ?? '';
    $description = $_POST['description'] ?? '';
    $externalLink = $_POST['external_link'] ?? '';

    // Handle photo upload
    $photoPath = null;
    if (isset($_FILES['project_photo']) && $_FILES['project_photo']['error'] === 0) {
        $photoDir = "../uploads/photos/$userId/";
        if (!is_dir($photoDir)) mkdir($photoDir, 0777, true);
        $photoPath = $photoDir . uniqid() . "_" . basename($_FILES['project_photo']['name']);
        if (!move_uploaded_file($_FILES['project_photo']['tmp_name'], $photoPath)) {
            die("Error uploading photo.");
        }
    }

    // Handle file upload
    $filePath = null;
    if (isset($_FILES['project_file']) && $_FILES['project_file']['error'] === 0) {
        $fileDir = "../uploads/files/$userId/";
        if (!is_dir($fileDir)) mkdir($fileDir, 0777, true);
        $filePath = $fileDir . uniqid() . "_" . basename($_FILES['project_file']['name']);
        if (!move_uploaded_file($_FILES['project_file']['tmp_name'], $filePath)) {
            die("Error uploading file.");
        }
    }

    // Insert the new project into the database
    $sql = "INSERT INTO projects (user_id, project_name, description, external_link, photo_path, file_path) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $userId, $projectName, $description, $externalLink, $photoPath, $filePath);

    if (!$stmt->execute()) {
        die("Error adding project: " . $stmt->error);
    }

    // Get the last inserted project ID
    $projectId = $stmt->insert_id;

    // Insert project members
    $members = explode(',', $projectMembers);
    $memberStmt = $conn->prepare("INSERT INTO project_members (project_id, member_name) VALUES (?, ?)");
    foreach ($members as $member) {
        $member = trim($member);
        $memberStmt->bind_param("is", $projectId, $member);
        if (!$memberStmt->execute()) {
            die("Error adding project member: " . $memberStmt->error);
        }
    }

    $_SESSION['upload_status'] = "Project added successfully!";
    header("Location: projects.php");
    exit;
}

// Display success message if any
if (isset($_SESSION['upload_status'])) {
    $successMessage = $_SESSION['upload_status'];
    unset($_SESSION['upload_status']);
}
?>

<?php include 'includes/head.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="container main-content py-4">
    <h2>Add New Project</h2>

    <!-- Display success message if available -->
    <?php if ($successMessage): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $successMessage; ?>
        </div>
    <?php endif; ?>

    <div class="card py-4 px-3 shadow-sm">
        <!-- Add Project Form -->
        <form action="add_project.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="project_name" class="form-label">Project Name</label>
                <input type="text" class="form-control" id="project_name" name="project_name" required>
            </div>

            <div class="mb-3">
                <label for="project_members" class="form-label">Members</label>
                <input type="text" class="form-control" id="project_members" name="project_members" placeholder="e.g., John Doe, Jane Smith">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label for="external_link" class="form-label">External Link</label>
                <input type="url" class="form-control" id="external_link" name="external_link">
            </div>

            <div class="mb-3">
                <label for="project_photo" class="form-label">Project Photo</label>
                <input type="file" class="form-control" id="project_photo" name="project_photo">
            </div>

            <div class="mb-3">
                <label for="project_file" class="form-label">Project File</label>
                <input type="file" class="form-control" id="project_file" name="project_file">
            </div>

            <button type="submit" class="btn btn-primary">Add Project</button>
            <a href="projects.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
