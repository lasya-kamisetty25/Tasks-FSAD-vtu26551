<?php
session_start();
include "db.php";

if($_SERVER['REQUEST_METHOD'] != 'POST'){
    die("Invalid request");
}

$user_id = $_SESSION['user_id'];
$lesson_id = intval($_POST['lesson_id']);
$course_id = intval($_POST['course_id']);
$answers = $_POST['answer'];

$score = 0;
$total = 0;

/* GET QUESTIONS */
$query = mysqli_query($conn,
    "SELECT id, correct_option
     FROM quizzes
     WHERE lesson_id='$lesson_id'"
);

while($row = mysqli_fetch_assoc($query)){

    $qid = $row['id'];
    $correct = $row['correct_option'];
    $total++;

    if(isset($answers[$qid]) && $answers[$qid] === $correct){
        $score++;
    }
}

/* STORE RESULT IN SESSION */
$_SESSION['quiz_result'] = [
    "score" => $score,
    "total" => $total,
    "lesson_id" => $lesson_id
];

/* REDIRECT BACK */
header("Location: learn.php?course_id=$course_id&lesson_id=$lesson_id");
exit();
?>