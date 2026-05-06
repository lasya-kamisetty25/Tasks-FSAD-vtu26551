<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];
$lesson_id = intval($_POST['lesson_id']);

/* MARK LESSON COMPLETE */
mysqli_query($conn,"
    INSERT IGNORE INTO lesson_progress(user_id, lesson_id)
    VALUES('$user_id','$lesson_id')
");

/* GET COURSE */
$course = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT section_id FROM lessons WHERE id='$lesson_id'"
));

$course_id = $course['section_id'];

/* TOTAL LESSONS */
$total = mysqli_num_rows(mysqli_query($conn,
    "SELECT id FROM lessons WHERE section_id='$course_id'"
));

/* COMPLETED LESSONS */
$done = mysqli_num_rows(mysqli_query($conn,
    "SELECT lp.lesson_id
     FROM lesson_progress lp
     JOIN lessons l ON lp.lesson_id = l.id
     WHERE lp.user_id='$user_id'
     AND l.section_id='$course_id'"
));

/* CERTIFICATE */
if($total > 0 && $total == $done){

    $check = mysqli_query($conn,
        "SELECT * FROM certificates
         WHERE user_id='$user_id'
         AND course_id='$course_id'"
    );

    if(mysqli_num_rows($check) == 0){

        mysqli_query($conn,"
            INSERT INTO certificates(user_id, course_id)
            VALUES('$user_id','$course_id')
        ");
    }
}
?>