<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$successMessage = "";

// Handle form submissions
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

    // Insert project into database
    $sql = "INSERT INTO projects (user_id, project_name, description, external_link, photo_path, file_path) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $userId, $projectName, $description, $externalLink, $photoPath, $filePath);

    if (!$stmt->execute()) {
        die("Error inserting project: " . $stmt->error);
    }

    // Insert project members
    $projectId = $conn->insert_id;
    $members = explode(',', $projectMembers);
    $memberStmt = $conn->prepare("INSERT INTO project_members (project_id, member_name) VALUES (?, ?)");
    foreach ($members as $member) {
        $member = trim($member);
        $memberStmt->bind_param("is", $projectId, $member);
        if (!$memberStmt->execute()) {
            die("Error inserting project member: " . $memberStmt->error);
        }
    }

    header("Location: student.php");
    exit;
}


// Retrieve and clear status messages
if (isset($_SESSION['upload_status'])) {
    $successMessage = $_SESSION['upload_status'];
    unset($_SESSION['upload_status']);
}
if (isset($_SESSION['delete_status'])) {
    $deleteMessage = $_SESSION['delete_status'];
    unset($_SESSION['delete_status']);
}

// Fetch uploaded projects
$sql = "SELECT p.*, GROUP_CONCAT(pm.member_name SEPARATOR ', ') AS members 
        FROM projects p
        LEFT JOIN project_members pm ON p.project_id = pm.project_id
        WHERE p.user_id = ?
        GROUP BY p.project_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>


<?php include 'includes/head.php'; ?>

<?php include 'includes/navbar.php'; ?>

<div class="container main-content py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4">Your Projects</h1>
        <div>
            <input type="text" id="search-bar" class="form-control d-inline-block me-2" style="width: 250px;" placeholder="Search projects..." oninput="searchProjects()">
            <script>
                function searchProjects() {
                    var searchTerm = document.getElementById('search-bar').value;

                    // Create an AJAX request
                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", "search_projects.php?search=" + encodeURIComponent(searchTerm), true);

                    // Handle the response from the server
                    xhr.onload = function() {
                        if (xhr.status == 200) {
                            // Update the projects container with the filtered projects
                            document.getElementById('projects-container').innerHTML = xhr.responseText;
                        }
                    };

                    // Send the request
                    xhr.send();
                }
            </script>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#upload-modal">
                <i class="bi bi-upload"></i> Upload New Project
            </button>
        </div>
    </div>

    <!-- Projects Section (this will be populated with initial data on page load) -->
    <div class="row g-4" id="projects-container">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="col-md-6 col-lg-4 project-card">
                <div class="card shadow-sm h-100">
                    <div class="image-container">
                        <img src="<?php echo htmlspecialchars($row['photo_path']); ?>" class="card-img-top project-image" alt="Project Photo">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['project_name']); ?></h5>
                        <p class="text-muted">
                            <strong>Members:</strong>
                        <ul class="list-unstyled mb-1">
                            <?php
                            $members = explode(',', $row['members']);
                            foreach ($members as $member) {
                                echo "<li><i class='bi bi-person-fill'></i> " . htmlspecialchars(trim($member)) . "</li>";
                            }
                            ?>
                        </ul>
                        </p>
                        <p class="text-muted mb-3">
                            <strong>Uploaded:</strong>
                            <?php echo date("F j, Y, g:i A", strtotime($row['upload_date'])); ?>
                        </p>
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo htmlspecialchars($row['file_path']); ?>" class="btn btn-sm btn-primary" download>
                                <i class="bi bi-download"></i> Download
                            </a>
                            <a href="<?php echo htmlspecialchars($row['external_link']); ?>" class="btn btn-sm btn-secondary" target="_blank">
                                <i class="bi bi-box-arrow-up-right"></i> View
                            </a>
                            <a href="edit_project.php?id=<?php echo $row['project_id']; ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#delete-modal-<?php echo $row['project_id']; ?>">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'includes/delete_modal.php'; ?>
        <?php } ?>


    </div>
</div>

<?php include 'includes/upload_modal.php'; ?>

<?php include 'includes/footer.php'; ?>