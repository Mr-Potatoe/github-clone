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

<!-- Main Content -->
<div class="container mt-5 main-content">
    <div class="text-center">
        <h1 class="display-4">Welcome, Admin</h1>
        <p class="lead text-muted">Use the navigation bar to manage projects and users.</p>
    </div>
    <div class="row text-center mt-4">
        <div class="col-md-6">
            <a href="projects.php" class="text-decoration-none">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Manage Projects</h5>
                        <p class="card-text">View, edit, and organize projects easily.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="users.php" class="text-decoration-none">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Manage Users</h5>
                        <p class="card-text">Add, remove, or update user roles and information.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>