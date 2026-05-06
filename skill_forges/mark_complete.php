<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(!isset($_GET['lesson_id']) || !isset($_GET['course_id'])){
    die("Invalid request");
}

$lesson_id = intval($_GET['lesson_id']);
$course_id = intval($_GET['course_id']);

/* CHECK ALREADY EXISTS */
$check = mysqli_query($conn,
    "SELECT * FROM lesson_progress
     WHERE user_id='$user_id'
     AND lesson_id='$lesson_id'"
);

if(mysqli_num_rows($check) == 0){

    mysqli_query($conn,
        "INSERT INTO lesson_progress (user_id, lesson_id)
         VALUES ('$user_id','$lesson_id')"
    );
}

/* REDIRECT BACK */
header("Location: learn.php?course_id=$course_id&lesson_id=$lesson_id");
exit();
?>