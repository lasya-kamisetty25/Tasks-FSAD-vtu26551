<?php
session_start();
include "db.php";

/* ADMIN PROTECTION */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

/* PLATFORM STATS */
$totalCourses = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM courses"));
$totalUsers = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users WHERE role='user'"));
$totalAdmins = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users WHERE role='admin'"));
$totalEnrollments = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM enrollments"));
$totalCertificates = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM certificates"));

$avgEnrollments = $totalCourses > 0 ? round($totalEnrollments / $totalCourses, 1) : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="css/style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<?php include "admin_sidebar.php"; ?>

<div class="main">

    <!-- HEADER -->
    <div class="header">
        <h1>Good Evening, Admin</h1>
        <p>Monitor and manage your LMS platform performance.</p>
    </div>

    <!-- TOP CARDS -->
    <div class="cards">

        <div class="card">
            <i class="fa fa-users"></i>
            <h2><?php echo $totalUsers; ?></h2>
            <p>Total Users</p>
        </div>

        <div class="card">
            <i class="fa fa-book"></i>
            <h2><?php echo $totalCourses; ?></h2>
            <p>Total Courses</p>
        </div>

        <div class="card">
            <i class="fa fa-graduation-cap"></i>
            <h2><?php echo $totalEnrollments; ?></h2>
            <p>Total Enrollments</p>
        </div>

        <div class="card">
            <i class="fa fa-certificate"></i>
            <h2><?php echo $totalCertificates; ?></h2>
            <p>Total Certificates</p>
        </div>

    </div>

    <!-- DASHBOARD GRID -->
    <div class="dashboard-grid">

        <!-- ANALYTICS -->
        <div class="analytics-box">
            <h2>
                <i class="fa fa-chart-bar"></i>
                Platform Analytics
            </h2>

            <canvas id="analyticsChart"></canvas>
        </div>

        <!-- SIDE WIDGETS -->
        <div class="side-widgets">

            <!-- USERS BREAKDOWN -->
            <div class="widget-box">
                <h3>Users Breakdown</h3>
                <p>Students: <?php echo $totalUsers; ?></p>
                <p>Admins: <?php echo $totalAdmins; ?></p>
            </div>

            <!-- COURSE INSIGHTS -->
            <div class="widget-box">
                <h3>Course Insights</h3>
                <p>Avg Enrollments: <?php echo $avgEnrollments; ?></p>
                <p>Certificates: <?php echo $totalCertificates; ?></p>
            </div>

            <!-- QUICK ACTIONS -->
            <div class="widget-box">
                <h3>Quick Actions</h3>

                <a href="addcourse.php" class="quick-btn">
                    Add Course
                </a>

                <a href="managecourses.php" class="quick-btn">
                    Manage Courses
                </a>

                <a href="addquiz.php" class="quick-btn">
                    Add Quiz
                </a>
            </div>

        </div>

    </div>

</div>

<!-- CHART -->
<script>
const ctx = document.getElementById('analyticsChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Users', 'Courses', 'Enrollments', 'Certificates'],
        datasets: [{
            label: 'Platform Stats',
            data: [
                <?php echo $totalUsers; ?>,
                <?php echo $totalCourses; ?>,
                <?php echo $totalEnrollments; ?>,
                <?php echo $totalCertificates; ?>
            ],
            backgroundColor: [
                '#7c3aed',
                '#8b5cf6',
                '#6366f1',
                '#a855f7'
            ],
            borderRadius: 10
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                ticks: {
                    color: '#ffffff'
                },
                grid: {
                    color: 'rgba(255,255,255,0.05)'
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    color: '#ffffff'
                },
                grid: {
                    color: 'rgba(255,255,255,0.05)'
                }
            }
        }
    }
});
</script>

</body>
</html>