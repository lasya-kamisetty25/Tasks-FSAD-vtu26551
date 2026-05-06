<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(!isset($_GET['course_id'])){
    die("Course not found");
}

$course_id = intval($_GET['course_id']);
$lesson_id = isset($_GET['lesson_id']) ? intval($_GET['lesson_id']) : 0;

/* FETCH COURSE */
$course = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM courses WHERE id='$course_id'"
));

if(!$course){
    die("Invalid course");
}

/* FETCH LESSONS */
$lessons = mysqli_query($conn,
    "SELECT * FROM lessons
     WHERE section_id='$course_id'
     ORDER BY id ASC"
);

/* DEFAULT FIRST LESSON */
if($lesson_id == 0){
    $firstLesson = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT id FROM lessons
         WHERE section_id='$course_id'
         ORDER BY id ASC LIMIT 1"
    ));

    if($firstLesson){
        $lesson_id = $firstLesson['id'];
    }
}

/* CURRENT LESSON */
$currentLesson = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM lessons WHERE id='$lesson_id'"
));

/* CHECK COMPLETION */
$isCompleted = mysqli_num_rows(mysqli_query($conn,
    "SELECT * FROM lesson_progress
     WHERE user_id='$user_id'
     AND lesson_id='$lesson_id'"
)) > 0;

/* QUIZ CHECK */
$quizCount = mysqli_num_rows(mysqli_query($conn,
    "SELECT * FROM quizzes WHERE lesson_id='$lesson_id'"
));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Learn - <?php echo $course['title']; ?></title>

    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .learn-container{
            display:flex;
            gap:20px;
        }

        .lesson-sidebar{
            width:300px;
            background:#0f172a;
            padding:20px;
            border-radius:15px;
            height:fit-content;
        }

        .lesson-sidebar a{
            display:block;
            padding:12px;
            margin:8px 0;
            color:white;
            text-decoration:none;
            border-radius:10px;
            background:#1e293b;
        }

        .lesson-sidebar a.active{
            background:linear-gradient(90deg,#7c3aed,#6366f1);
        }

        .lesson-content{
            flex:1;
            background:#1e293b;
            padding:25px;
            border-radius:15px;
        }

        .video-box iframe{
            width:100%;
            height:400px;
            border-radius:15px;
        }

        .complete-btn{
            display:inline-block;
            margin-top:20px;
            padding:12px 20px;
            background:linear-gradient(90deg,#22c55e,#16a34a);
            color:white;
            border:none;
            border-radius:10px;
            text-decoration:none;
        }

        .quiz-btn{
            display:inline-block;
            margin-top:20px;
            margin-left:10px;
            padding:12px 20px;
            background:linear-gradient(90deg,#f59e0b,#d97706);
            color:white;
            border:none;
            border-radius:10px;
            text-decoration:none;
        }

        .certificate-btn{
            display:inline-block;
            margin-top:20px;
            padding:12px 20px;
            background:linear-gradient(90deg,#2563eb,#1d4ed8);
            color:white;
            border:none;
            border-radius:10px;
            text-decoration:none;
        }
    </style>
</head>

<body>

<div class="sidebar">
    <div class="logo">SkillForge</div>

    <a href="dashboard.php">
        <i class="fa fa-home"></i> Dashboard
    </a>

    <a href="mycourses.php" class="active">
        <i class="fa fa-book"></i> My Courses
    </a>

    <a href="logout.php">
        <i class="fa fa-sign-out-alt"></i> Logout
    </a>
</div>

<div class="main">

    <h1><?php echo $course['title']; ?></h1>

    <div class="learn-container">

        <!-- LESSON SIDEBAR -->
        <div class="lesson-sidebar">

            <h3>Course Lessons</h3>

            <?php
            mysqli_data_seek($lessons, 0);

            while($lesson = mysqli_fetch_assoc($lessons)){
            ?>

                <a href="learn.php?course_id=<?php echo $course_id; ?>&lesson_id=<?php echo $lesson['id']; ?>"
                   class="<?php echo ($lesson_id == $lesson['id']) ? 'active' : ''; ?>">
                    <?php echo $lesson['title']; ?>
                </a>

            <?php } ?>

        </div>

        <!-- LESSON CONTENT -->
        <div class="lesson-content">

            <?php if($currentLesson){ ?>

                <h2><?php echo $currentLesson['title']; ?></h2>

                <?php if(!empty($currentLesson['video_url'])){ ?>
                    <div class="video-box">
                        <iframe src="<?php echo $currentLesson['video_url']; ?>"
                                frameborder="0"
                                allowfullscreen></iframe>
                    </div>
                <?php } ?>

                <p style="margin-top:20px;">
                    <?php echo nl2br($currentLesson['content']); ?>
                </p>

                <!-- MARK COMPLETE -->
                <?php if(!$isCompleted){ ?>
                    <a href="mark_complete.php?course_id=<?php echo $course_id; ?>&lesson_id=<?php echo $lesson_id; ?>"
                       class="complete-btn">
                        Mark as Complete
                    </a>
                <?php } else { ?>
                    <button class="complete-btn" disabled>
                        Completed ✅
                    </button>
                <?php } ?>

                <!-- QUIZ -->
                <?php if($quizCount > 0){ ?>
                    <a href="quiz.php?lesson_id=<?php echo $lesson_id; ?>"
                       class="quiz-btn">
                        Take Quiz
                    </a>
                <?php } ?>

                <!-- QUIZ RESULT -->
                <?php
                if(isset($_SESSION['quiz_result']) &&
                   $_SESSION['quiz_result']['lesson_id'] == $lesson_id){

                    $quiz = $_SESSION['quiz_result'];
                ?>
                    <div style="margin-top:20px; padding:15px; background:#0f172a; border-radius:10px;">
                        <h3>
                            Quiz Score:
                            <?php echo $quiz['score']; ?>/<?php echo $quiz['total']; ?>
                        </h3>
                    </div>
                <?php
                    unset($_SESSION['quiz_result']);
                }
                ?>

                <!-- CERTIFICATE -->
                <?php
                $certCheck = mysqli_num_rows(mysqli_query($conn,
                    "SELECT * FROM certificates
                     WHERE user_id='$user_id'
                     AND course_id='$course_id'"
                ));

                if($certCheck > 0){
                ?>
                    <br>
                    <a href="generate_certificate.php?course_id=<?php echo $course_id; ?>"
                       class="certificate-btn"
                       target="_blank">
                        Download Certificate 🎓
                    </a>
                <?php } ?>

            <?php } ?>

        </div>

    </div>

</div>

</body>
</html>