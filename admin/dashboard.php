<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch analytics data
$totalProjects = $conn->query("SELECT COUNT(*) AS total FROM projects")->fetch_assoc()['total'];

$projectsByDate = $conn->query("
    SELECT DATE(upload_date) AS upload_date, COUNT(*) AS total 
    FROM projects 
    GROUP BY DATE(upload_date) 
    ORDER BY upload_date
")->fetch_all(MYSQLI_ASSOC);

$topMembers = $conn->query("
    SELECT pm.member_name, COUNT(pm.project_id) AS total_projects 
    FROM project_members pm
    GROUP BY pm.member_name 
    ORDER BY total_projects DESC 
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

$projectsByUsers = $conn->query("
    SELECT u.username, COUNT(p.project_id) AS total_projects 
    FROM users u
    LEFT JOIN projects p ON u.user_id = p.user_id
    GROUP BY u.username
")->fetch_all(MYSQLI_ASSOC);
?>


<?php include 'includes/head.php'; ?>
<?php include 'includes/navbar.php'; ?>
<div class="container mt-5">
    <h1 class="mb-4 text-center">Dashboard</h1>

    <!-- Overview Section -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-muted mb-3">
                        <i class="bi bi-diagram-3-fill text-primary me-2"></i>Total Projects
                    </h5>
                    <h2 class="display-5 text-primary fw-bold"><?php echo $totalProjects; ?></h2>
                </div>
            </div>
        </div>
        <!-- Add more cards here for stats like Total Members, Active Users, etc. -->
    </div>

    <!-- Charts Section -->
    <div class="row">
        <!-- Projects by Upload Date -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Projects by Upload Date</h5>
                </div>
                <div class="card-body">
                    <div id="projectsByDateChart"></div>
                </div>
            </div>
        </div>

        <!-- Top Members -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Top Members by Participation</h5>
                </div>
                <div class="card-body">
                    <div id="topMembersChart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Projects by Users -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Projects by Users</h5>
                </div>
                <div class="card-body">
                    <div id="projectsByUsersChart"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Data for Projects by Upload Date
    const projectsByDate = {
        categories: <?php echo json_encode(array_column($projectsByDate, 'upload_date')); ?>,
        series: <?php echo json_encode(array_column($projectsByDate, 'total')); ?>
    };

    new ApexCharts(document.querySelector("#projectsByDateChart"), {
        chart: {
            type: 'line',
            height: 300
        },
        series: [{
            name: 'Projects',
            data: projectsByDate.series
        }],
        xaxis: {
            categories: projectsByDate.categories
        },
        colors: ['#0d6efd'],
        tooltip: {
            theme: 'dark'
        }
    }).render();

    // Data for Top Members
    const topMembers = {
        categories: <?php echo json_encode(array_column($topMembers, 'member_name')); ?>,
        series: <?php echo json_encode(array_column($topMembers, 'total_projects')); ?>
    };

    new ApexCharts(document.querySelector("#topMembersChart"), {
        chart: {
            type: 'bar',
            height: 300
        },
        series: [{
            name: 'Projects',
            data: topMembers.series
        }],
        xaxis: {
            categories: topMembers.categories
        },
        colors: ['#198754'],
        tooltip: {
            theme: 'dark'
        }
    }).render();

    // Data for Projects by Users
    const projectsByUsers = {
        categories: <?php echo json_encode(array_column($projectsByUsers, 'username')); ?>,
        series: <?php echo json_encode(array_column($projectsByUsers, 'total_projects')); ?>
    };

    new ApexCharts(document.querySelector("#projectsByUsersChart"), {
        chart: {
            type: 'pie',
            height: 300
        },
        series: projectsByUsers.series,
        labels: projectsByUsers.categories,
        colors: ['#0dcaf0', '#ffc107', '#dc3545', '#198754', '#0d6efd'],
        tooltip: {
            theme: 'dark'
        }
    }).render();
</script>

<?php include 'includes/footer.php'; ?>