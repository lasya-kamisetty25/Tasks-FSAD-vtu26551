<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* FETCH USER DATA */
$stmt = $conn->prepare("
    SELECT name, email, role
    FROM users
    WHERE id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>

    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .profile-box{
            max-width:700px;
            margin:40px auto;
            background:linear-gradient(145deg,#1e293b,#0f172a);
            padding:30px;
            border-radius:20px;
            box-shadow:0 10px 30px rgba(0,0,0,.6);
        }

        .profile-box h2{
            text-align:center;
            margin-bottom:25px;
        }

        .profile-item{
            margin-bottom:20px;
            padding:15px;
            background:#0f172a;
            border-radius:12px;
        }

        .profile-item strong{
            display:block;
            margin-bottom:6px;
            color:#7c3aed;
        }

        .profile-item span{
            color:#cbd5e1;
        }
    </style>
</head>

<body>

<div class="sidebar">

    <div class="logo">SkillForge</div>

    <a href="dashboard.php">
        <i class="fa fa-home"></i> Overview
    </a>

    <a href="mycourses.php">
        <i class="fa fa-book"></i> My Courses
    </a>

    <a href="courses.php">
        <i class="fa fa-graduation-cap"></i> Explore Courses
    </a>

    <a href="profile.php" class="active">
        <i class="fa fa-user"></i> Profile
    </a>

    <a href="logout.php">
        <i class="fa fa-sign-out-alt"></i> Logout
    </a>

</div>

<div class="main">

    <div class="profile-box">

        <h2>My Profile</h2>

        <div class="profile-item">
            <strong>Full Name</strong>
            <span><?php echo htmlspecialchars($user['name']); ?></span>
        </div>

        <div class="profile-item">
            <strong>Email Address</strong>
            <span><?php echo htmlspecialchars($user['email']); ?></span>
        </div>

        <div class="profile-item">
            <strong>Role</strong>
            <span><?php echo ucfirst($user['role']); ?></span>
        </div>

    </div>

</div>

</body>
</html>