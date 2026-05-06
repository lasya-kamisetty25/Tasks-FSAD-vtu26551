<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(!isset($_GET['course_id'])){
    die("Invalid course");
}

$course_id = intval($_GET['course_id']);

/* CHECK EXISTING ENROLLMENT */
$check = mysqli_query($conn,
    "SELECT * FROM enrollments
     WHERE user_id='$user_id'
     AND course_id='$course_id'"
);

if(mysqli_num_rows($check) == 0){

    mysqli_query($conn,
        "INSERT INTO enrollments(user_id, course_id)
         VALUES('$user_id','$course_id')"
    );
}

/* REDIRECT TO MY COURSES */
header("Location: mycourses.php");
exit();
?>