<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];

/* USER STATS */
$enrolled = mysqli_num_rows(mysqli_query($conn,
    "SELECT * FROM enrollments WHERE user_id='$user_id'"
));

$certificates = mysqli_num_rows(mysqli_query($conn,
    "SELECT * FROM certificates WHERE user_id='$user_id'"
));

/* LESSON PROGRESS */
$total_lessons_query = mysqli_query($conn,"
    SELECT COUNT(lessons.id) AS total
    FROM lessons
    JOIN enrollments ON lessons.section_id = enrollments.course_id
    WHERE enrollments.user_id='$user_id'
");

$total_lessons = mysqli_fetch_assoc($total_lessons_query)['total'] ?? 0;

$completed_lessons = mysqli_num_rows(mysqli_query($conn,"
    SELECT * FROM lesson_progress
    WHERE user_id='$user_id'
"));

$progress = ($total_lessons > 0)
    ? round(($completed_lessons / $total_lessons) * 100)
    : 0;

$date = date("l, d M Y");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        *{
            box-sizing:border-box;
            font-family:'Poppins',sans-serif;
        }

        body{
            margin:0;
            background:linear-gradient(135deg,#020617,#0f172a,#020617);
            color:white;
        }

        .main{
            margin-left:280px;
            padding:35px;
            min-height:100vh;
        }

        /* HEADER */
        .header h1{
            font-size:28px;
            font-weight:700;
            margin-bottom:8px;
        }

        .header p{
            font-size:15px;
            color:#b8b8d0;
            margin:4px 0;
        }

        /* PROFILE */
        .profile-box{
            display:flex;
            align-items:center;
            gap:18px;
            margin:25px 0;
        }

        .profile-box img{
            width:95px;
            height:95px;
            border-radius:50%;
            border:4px solid #7c3aed;
            background:white;
            object-fit:cover;
        }

        .profile-info h2{
            font-size:22px;
            margin:0;
        }

        .profile-info p{
            font-size:15px;
            color:#d1d5db;
        }

        /* GRID */
        .overview-grid{
            display:grid;
            grid-template-columns:2.8fr 1fr;
            gap:20px;
            margin-top:25px;
        }

        /* CARDS */
        .cards{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:20px;
            margin-bottom:25px;
        }

        .card{
            background:rgba(255,255,255,0.08);
            padding:25px;
            border-radius:22px;
            min-height:170px;
            box-shadow:0 8px 22px rgba(0,0,0,0.25);
        }

        .card i{
            font-size:26px;
            color:#7c3aed;
            margin-bottom:18px;
        }

        .card h2{
            font-size:48px;
            margin:0;
            font-weight:700;
        }

        .card p{
            font-size:15px;
            margin-top:12px;
            color:#d1d5db;
        }

        /* RIGHT PANEL */
        .right-panel{
            display:flex;
            flex-direction:column;
            gap:18px;
        }

        .stat-box{
            background:rgba(255,255,255,0.08);
            padding:22px;
            border-radius:20px;
            box-shadow:0 8px 20px rgba(0,0,0,0.22);
        }

        .stat-box h3{
            font-size:18px;
            margin-bottom:10px;
        }

        .stat-box p{
            font-size:15px;
            color:#d1d5db;
            margin:6px 0;
        }

        /* ANALYTICS */
        .analytics-box{
            background:rgba(255,255,255,0.08);
            padding:25px;
            border-radius:22px;
            margin-bottom:25px;
        }

        .analytics-box h2{
            font-size:22px;
            margin-bottom:18px;
        }

        /* PROGRESS BAR */
        .progress-bar{
            width:100%;
            height:12px;
            background:#1e293b;
            border-radius:10px;
            overflow:hidden;
            margin-top:10px;
        }

        .progress-fill{
            height:100%;
            background:linear-gradient(90deg,#7c3aed,#6366f1);
            width:<?= $progress ?>%;
            border-radius:10px;
        }

        /* CHART */
        #analyticsChart{
            max-height:300px !important;
        }

        /* RESPONSIVE */
        @media(max-width:1200px){
            .cards{
                grid-template-columns:repeat(2,1fr);
            }

            .overview-grid{
                grid-template-columns:1fr;
            }
        }

        @media(max-width:768px){
            .main{
                margin-left:0;
                padding:20px;
            }

            .cards{
                grid-template-columns:1fr;
            }

            .profile-box{
                flex-direction:column;
                text-align:center;
            }
        }
    </style>
</head>

<body>

<!-- USER SIDEBAR -->
<div class="sidebar">

    <div class="logo">SkillForge</div>

    <?php $current = basename($_SERVER['PHP_SELF']); ?>

    <a href="dashboard.php" class="<?= ($current=='dashboard.php') ? 'active' : ''; ?>">
        <i class="fa fa-home"></i> Overview
    </a>

    <a href="mycourses.php">
        <i class="fa fa-book"></i> My Courses
    </a>

    <a href="courses.php">
        <i class="fa fa-graduation-cap"></i> Explore Courses
    </a>

    <a href="profile.php">
        <i class="fa fa-user"></i> Profile
    </a>

    <a href="logout.php">
        <i class="fa fa-sign-out-alt"></i> Logout
    </a>

</div>

<!-- MAIN -->
<div class="main">

    <div class="header">
        <h1>Welcome back, <?php echo htmlspecialchars($user_name); ?></h1>
        <p>Ready to continue your learning journey today?</p>
        <p><?php echo $date; ?></p>
    </div>

    <!-- PROFILE -->
    <div class="profile-box">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Profile">
        <div class="profile-info">
            <h2>Premium Learner</h2>
            <p>Keep upgrading your skills daily</p>
        </div>
    </div>

    <div class="overview-grid">

        <!-- LEFT -->
        <div>

            <!-- TOP CARDS -->
            <div class="cards">

                <div class="card">
                    <i class="fa fa-book-open"></i>
                    <h2><?php echo $enrolled; ?></h2>
                    <p>Enrolled Courses</p>
                </div>

                <div class="card">
                    <i class="fa fa-certificate"></i>
                    <h2><?php echo $certificates; ?></h2>
                    <p>Certificates Earned</p>
                </div>

                <div class="card">
                    <i class="fa fa-chart-line"></i>
                    <h2><?php echo $progress; ?>%</h2>
                    <p>Learning Progress</p>
                </div>

            </div>

            <!-- PROGRESS -->
            <div class="analytics-box">
                <h2>Learning Completion</h2>
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                </div>
            </div>

            <!-- CHART -->
            <div class="analytics-box">
                <h2>Learning Analytics</h2>
                <canvas id="analyticsChart"></canvas>
            </div>

        </div>

        <!-- RIGHT -->
        <div class="right-panel">

            <div class="stat-box">
                <h3>Learning Streak</h3>
                <p>1 Days Active</p>
            </div>

            <div class="stat-box">
                <h3>Learning Time</h3>
                <p>0 Hours Learned</p>
            </div>

            <div class="stat-box">
                <h3>Goal Progress</h3>
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                </div>
                <p style="margin-top:10px;"><?php echo $progress; ?>% Completed</p>
            </div>

            <div class="stat-box">
                <h3>Motivation</h3>
                <p>Consistency beats talent.</p>
                <p>Keep learning daily</p>
            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('analyticsChart').getContext('2d');

new Chart(ctx,{
    type:'doughnut',
    data:{
        labels:['Completed','Remaining'],
        datasets:[{
            data:[<?= $progress ?>, <?= 100-$progress ?>],
            backgroundColor:['#7c3aed','#1e293b'],
            borderWidth:0
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{
                labels:{
                    color:'white',
                    font:{
                        size:14
                    }
                }
            }
        }
    }
});
</script>

</body>
</html>