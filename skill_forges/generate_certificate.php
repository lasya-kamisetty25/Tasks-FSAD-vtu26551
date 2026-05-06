<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];
$course_id = intval($_GET['course_id']);

$user = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM users WHERE id='$user_id'"
));

$course = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM courses WHERE id='$course_id'"
));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Certificate</title>

    <style>
        body{
            text-align:center;
            font-family:Arial;
            padding:50px;
        }

        .certificate{
            border:10px solid gold;
            padding:40px;
        }
    </style>
</head>

<body>

<div class="certificate">

    <h1>Certificate of Completion</h1>

    <p>This is to certify that</p>

    <h2>
        <?php echo $user['name']; ?>
    </h2>

    <p>has completed</p>

    <h3>
        <?php echo $course['title']; ?>
    </h3>

    <p>
        Date: <?php echo date("d M Y"); ?>
    </p>

</div>

<br>

<button onclick="window.print()">
    Print / Save
</button>

</body>
</html>