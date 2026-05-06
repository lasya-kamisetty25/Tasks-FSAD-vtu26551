<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

/* GET LESSON ID */
if(!isset($_GET['lesson_id'])){
    echo "Lesson not found";
    exit();
}

$lesson_id = intval($_GET['lesson_id']);

/* FETCH QUIZ QUESTIONS */
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE lesson_id=?");
$stmt->bind_param("i",$lesson_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    echo "No quiz available for this lesson";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz</title>

    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        .quiz-box{
            max-width:750px;
            margin:40px auto;
            background: linear-gradient(145deg,#1e293b,#0f172a);
            padding:25px;
            border-radius:15px;
            color:white;
            box-shadow:0 10px 30px rgba(0,0,0,.5);
        }

        .quiz-box h2{
            text-align:center;
            margin-bottom:20px;
        }

        .question{
            margin-bottom:20px;
            padding:15px;
            background:#020617;
            border-radius:10px;
        }

        label{
            display:block;
            margin:6px 0;
            cursor:pointer;
        }

        input[type="radio"]{
            margin-right:8px;
        }

        button{
            margin-top:20px;
            padding:12px;
            width:100%;
            border:none;
            border-radius:10px;
            background:linear-gradient(90deg,#7c3aed,#6366f1);
            color:white;
            font-size:15px;
            cursor:pointer;
        }

        button:hover{
            opacity:.9;
        }
    </style>
</head>

<body>

<div class="quiz-box">

    <h2>Quiz 📝</h2>

    <form method="POST" action="submit_quiz.php">

        <?php while($q = $result->fetch_assoc()){ ?>

        <div class="question">

            <p><b><?php echo $q['question']; ?></b></p>

            <label>
                <input type="radio"
                       name="answer[<?php echo $q['id']; ?>]"
                       value="option1">
                <?php echo $q['option1']; ?>
            </label>

            <label>
                <input type="radio"
                       name="answer[<?php echo $q['id']; ?>]"
                       value="option2">
                <?php echo $q['option2']; ?>
            </label>

            <label>
                <input type="radio"
                       name="answer[<?php echo $q['id']; ?>]"
                       value="option3">
                <?php echo $q['option3']; ?>
            </label>

            <label>
                <input type="radio"
                       name="answer[<?php echo $q['id']; ?>]"
                       value="option4">
                <?php echo $q['option4']; ?>
            </label>

        </div>

        <?php } ?>

        <input type="hidden"
               name="lesson_id"
               value="<?php echo $lesson_id; ?>">

        <button type="submit">Submit Quiz</button>

    </form>

</div>

</body>
</html>