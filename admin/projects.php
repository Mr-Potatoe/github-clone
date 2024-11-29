<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<?php include 'includes/head.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="container main-content mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Projects</h1>
        <a href="add_project.php" class="btn btn-primary">Add New Project</a>
    </div>
    <div class="row g-4">
        <?php
        $result = $conn->query("
            SELECT p.*, u.username, 
                   GROUP_CONCAT(pm.member_name SEPARATOR ', ') AS members 
            FROM projects p
            LEFT JOIN users u ON p.user_id = u.user_id
            LEFT JOIN project_members pm ON p.project_id = pm.project_id
            GROUP BY p.project_id
        ");
        while ($row = $result->fetch_assoc()) {
        ?>
            <div class="col-md-6 col-lg-4 project-card">
                <div class="card h-100 shadow-sm">
                    <div class="image-container">
                        <?php if (!empty($row['photo_path']) && file_exists($row['photo_path'])): ?>
                            <img src="<?php echo htmlspecialchars($row['photo_path']); ?>" class="card-img-top project-image" alt="<?php echo htmlspecialchars($row['project_name']); ?>">
                        <?php else: ?>
                            <img src="placeholder.jpg" class="card-img-top project-image" alt="Placeholder Image">
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['project_name']); ?></h5>
                        <p class="card-text">
                            <small class="text-muted">Uploaded by: <?php echo htmlspecialchars($row['username']); ?></small><br>
                            <small class="text-muted">Upload date: <?php echo htmlspecialchars($row['upload_date']); ?></small>
                        </p>
                        <p class="card-text">
                            <?php echo nl2br(htmlspecialchars($row['description'])); ?>
                        </p>
                        <p class="card-text">
                            <strong>Members:</strong> <?php echo htmlspecialchars($row['members'] ?: 'None'); ?>
                        </p>
                        <?php if (!empty($row['external_link'])): ?>
                            <a href="<?php echo htmlspecialchars($row['external_link']); ?>" class="btn btn-sm btn-secondary" target="_blank">View Project</a>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-end">
                        <a href="edit_project.php?id=<?php echo $row['project_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $row['project_id']; ?>">Delete</button>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this project? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="delete_project.php">
                    <input type="hidden" name="project_id" id="deleteProjectId">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Attach project ID to the modal when triggered
    document.addEventListener('DOMContentLoaded', () => {
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget; // Button that triggered the modal
            const projectId = button.getAttribute('data-id'); // Extract project ID from data-* attributes
            const deleteInput = document.getElementById('deleteProjectId'); // Hidden input in the form
            deleteInput.value = projectId; // Set project ID
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
