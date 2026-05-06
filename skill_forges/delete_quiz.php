<?php
session_start();
include "db.php";

/* ADMIN PROTECTION */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

/* CHECK QUIZ ID */
if(!isset($_GET['id'])){
    die("Invalid request");
}

$id = intval($_GET['id']);

/* DELETE QUIZ */
$stmt = $conn->prepare("DELETE FROM quizzes WHERE id=?");
$stmt->bind_param("i", $id);

if($stmt->execute()){
    echo "success";
} else {
    echo "error";
}
?>