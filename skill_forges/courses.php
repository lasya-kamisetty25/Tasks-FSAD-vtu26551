<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* FETCH COURSES */
$sql = "SELECT * FROM courses WHERE status='Active'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Explore Courses</title>

    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .course-grid{
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
            gap:25px;
            margin-top:30px;
        }

        .course-card{
            background:linear-gradient(145deg,#1e293b,#0f172a);
            border-radius:18px;
            overflow:hidden;
            box-shadow:0 10px 30px rgba(0,0,0,.6);
            transition:.3s;
            display:flex;
            flex-direction:column;
        }

        .course-card:hover{
            transform:translateY(-6px);
        }

        .course-img{
            width:100%;
            height:170px;
            object-fit:cover;
        }

        .course-content{
            padding:15px;
            display:flex;
            flex-direction:column;
            flex-grow:1;
        }

        .badge{
            background:linear-gradient(90deg,#7c3aed,#6366f1);
            padding:5px 10px;
            border-radius:8px;
            font-size:12px;
            display:inline-block;
            margin-bottom:10px;
        }

        .price{
            color:#22c55e;
            font-weight:600;
            margin:10px 0;
        }

        .btn{
            margin-top:auto;
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

    <a href="courses.php" class="active">
        <i class="fa fa-graduation-cap"></i> Explore Courses
    </a>

    <a href="logout.php">
        <i class="fa fa-sign-out-alt"></i> Logout
    </a>

</div>

<div class="main">

    <div class="header">
        <h1>Explore Courses</h1>
        <p>Choose a course and start learning today.</p>
    </div>

    <div class="course-grid">

        <?php
        if(mysqli_num_rows($result) > 0){

            while($row = mysqli_fetch_assoc($result)){

                $course_id = $row['id'];

                /* CHECK ENROLLMENT */
                $check = mysqli_query($conn,
                    "SELECT * FROM enrollments
                     WHERE user_id='$user_id'
                     AND course_id='$course_id'"
                );

                $isEnrolled = mysqli_num_rows($check) > 0;

                $imagePath = !empty($row['image']) && file_exists("uploads/".$row['image'])
                    ? "uploads/".$row['image']
                    : "https://via.placeholder.com/400x200?text=Course";
        ?>

        <div class="course-card">

            <img src="<?php echo $imagePath; ?>"
                 class="course-img">

            <div class="course-content">

                <span class="badge">
                    <?php echo $row['category']; ?>
                </span>

                <h3>
                    <i class="fa fa-book-open"></i>
                    <?php echo $row['title']; ?>
                </h3>

                <p>
                    <?php echo substr($row['description'],0,100).'...'; ?>
                </p>

                <div class="price">
                    ₹<?php echo $row['price']; ?>
                </div>

                <?php if(!$isEnrolled){ ?>

                    <a href="enroll.php?course_id=<?php echo $course_id; ?>"
                       class="btn">
                        Enroll Now
                    </a>

                <?php } else { ?>

                    <a href="learn.php?course_id=<?php echo $course_id; ?>"
                       class="btn">
                        Continue Learning
                    </a>

                <?php } ?>

            </div>

        </div>

        <?php
            }

        } else {

            echo "<h3>No active courses available.</h3>";
        }
        ?>

    </div>

</div>

</body>
</html>