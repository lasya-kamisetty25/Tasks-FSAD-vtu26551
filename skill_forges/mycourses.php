<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT
        courses.id,
        courses.title,
        courses.description,
        courses.category,
        courses.price,
        courses.image
    FROM enrollments
    JOIN courses ON enrollments.course_id = courses.id
    WHERE enrollments.user_id=?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Courses</title>

    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* GRID */
        .course-grid{
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
            gap:25px;
            margin-top:30px;
        }

        /* CARD */
        .course-card{
            background: linear-gradient(145deg,#1e293b,#0f172a);
            border-radius:18px;
            overflow:hidden;
            box-shadow:0 10px 30px rgba(0,0,0,.6);
            transition:.3s;
            border:1px solid rgba(255,255,255,0.05);
            display:flex;
            flex-direction:column;
        }

        .course-card:hover{
            transform:translateY(-6px);
            box-shadow:0 15px 40px rgba(124,58,237,.3);
        }

        /* IMAGE */
        .course-img{
            width:100%;
            height:150px;
            object-fit:cover;
        }

        /* CONTENT */
        .course-content{
            padding:12px;
            display:flex;
            flex-direction:column;
            gap:6px;
        }

        /* BADGE */
        .badge{
            background:linear-gradient(90deg,#7c3aed,#6366f1);
            padding:5px 10px;
            border-radius:8px;
            font-size:12px;
            display:inline-block;
        }

        /* TITLE */
        .course-content h3{
            font-size:16px;
            margin-bottom:5px;
        }

        /* DESCRIPTION */
        .course-content p{
            font-size:13px;
            color:#cbd5e1;
            line-height:1.4;
        }

        /* PRICE */
        .price{
            color:#22c55e;
            font-weight:600;
            margin-top:5px;
        }

        /* BUTTON */
        .btn{
            margin-top:10px;
            padding:10px;
            font-size:14px;
            background:linear-gradient(90deg,#7c3aed,#6366f1);
            border:none;
            border-radius:10px;
            color:white;
            cursor:pointer;
            text-align:center;
            text-decoration:none;
        }

        .btn:hover{
            transform:scale(1.03);
        }
    </style>
</head>

<body>

<div class="sidebar">

    <div class="logo">SkillForge</div>

    <a href="dashboard.php">
        <i class="fa fa-home"></i> Overview
    </a>

    <a href="mycourses.php" class="active">
        <i class="fa fa-book"></i> My Courses
    </a>

    <a href="courses.php">
        <i class="fa fa-graduation-cap"></i> Explore Courses
    </a>

    <a href="logout.php">
        <i class="fa fa-sign-out-alt"></i> Logout
    </a>

</div>

<div class="main">

    <div class="header">
        <h1>Continue Learning</h1>
        <p>Select a course to start learning.</p>
    </div>

    <div class="course-grid">

        <?php
        if($result->num_rows > 0){

            while($row = $result->fetch_assoc()){

                $imagePath = !empty($row['image']) && file_exists("uploads/".$row['image'])
                    ? "uploads/".$row['image']
                    : "https://via.placeholder.com/400x200?text=Course";
        ?>

        <div class="course-card">

            <img src="<?php echo $imagePath; ?>" class="course-img">

            <div class="course-content">

                <span class="badge">
                    <?php echo $row['category']; ?>
                </span>

                <h3>
                    <i class="fa fa-book-open"></i>
                    <?php echo $row['title']; ?>
                </h3>

                <p>
                    <?php echo substr($row['description'],0,80).'...'; ?>
                </p>

                <div class="price">
                    ₹<?php echo $row['price']; ?>
                </div>

                <a href="learn.php?course_id=<?php echo $row['id']; ?>"
                   class="btn">
                    <i class="fa fa-play"></i> Start Learning
                </a>

            </div>

        </div>

        <?php
            }

        } else {

            echo "<h3>No enrolled courses yet.</h3>";
        }
        ?>

    </div>

</div>

</body>
</html>