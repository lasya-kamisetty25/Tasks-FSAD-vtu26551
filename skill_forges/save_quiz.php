<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role']!='admin'){
    header("Location: login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == "POST"){

    $lesson_id = $_POST['lesson_id'];
    $course_id = $_POST['course_id'] ?? 0;

    $question = $_POST['question'];
    $option1 = $_POST['option1'];
    $option2 = $_POST['option2'];
    $option3 = $_POST['option3'];
    $option4 = $_POST['option4'];
    $correct = $_POST['correct_option'];

    /* EXTRA SAFETY */
    $correct = trim($correct);

    $stmt = $conn->prepare("
        INSERT INTO quizzes
        (lesson_id, question, option1, option2, option3, option4, correct_option)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issssss",
        $lesson_id,
        $question,
        $option1,
        $option2,
        $option3,
        $option4,
        $correct
    );

    if($stmt->execute()){

        /* KEEP COURSE CONTEXT */
        header("Location: addquiz.php?course_id=$course_id&success=1");

    } else {

        header("Location: addquiz.php?course_id=$course_id&error=1");

    }

    exit();
}
?>