<?php
session_start();
include "db.php";

/* ADMIN PROTECTION */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

/* VALIDATE COURSE ID */
if(!isset($_GET['id'])){
    die("Invalid request");
}

$id = intval($_GET['id']);

/* FETCH COURSE IMAGE */
$course = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT image FROM courses WHERE id='$id'"
));

/* DELETE IMAGE FILE */
if($course && !empty($course['image'])){

    $imagePath = "uploads/" . $course['image'];

    if(file_exists($imagePath)){
        unlink($imagePath);
    }
}

/* DELETE RELATED QUIZZES */
mysqli_query($conn,
    "DELETE quizzes
     FROM quizzes
     JOIN lessons ON quizzes.lesson_id = lessons.id
     WHERE lessons.section_id='$id'"
);

/* DELETE LESSONS */
mysqli_query($conn,
    "DELETE FROM lessons
     WHERE section_id='$id'"
);

/* DELETE ENROLLMENTS */
mysqli_query($conn,
    "DELETE FROM enrollments
     WHERE course_id='$id'"
);

/* DELETE CERTIFICATES */
mysqli_query($conn,
    "DELETE FROM certificates
     WHERE course_id='$id'"
);

/* DELETE COURSE */
mysqli_query($conn,
    "DELETE FROM courses
     WHERE id='$id'"
);

/* REDIRECT */
header("Location: managecourses.php");
exit();
?>