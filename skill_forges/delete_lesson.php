<?php
session_start();
include "db.php";

/* ADMIN PROTECTION */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

/* CHECK LESSON ID */
if(!isset($_GET['id'])){
    header("Location: manage_lessons.php");
    exit();
}

$lesson_id = intval($_GET['id']);

/* FETCH VIDEO FILE */
$stmt = $conn->prepare("SELECT video_url FROM lessons WHERE id=?");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){

    $lesson = $result->fetch_assoc();

    /* DELETE VIDEO FILE */
    if(!empty($lesson['video_url']) && file_exists("videos/" . $lesson['video_url'])){
        unlink("videos/" . $lesson['video_url']);
    }

    /* DELETE LESSON */
    $delete = $conn->prepare("DELETE FROM lessons WHERE id=?");
    $delete->bind_param("i", $lesson_id);
    $delete->execute();
    $delete->close();
}

$stmt->close();

/* REDIRECT BACK */
header("Location: manage_lessons.php");
exit();
?>