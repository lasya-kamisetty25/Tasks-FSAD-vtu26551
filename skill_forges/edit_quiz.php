<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "db.php";

/* Admin Protection */
if(!isset($_SESSION['user_id']) || $_SESSION['role']!='admin'){
    header("Location: login.php");
    exit();
}

/* Check ID */
if(!isset($_GET['id'])){
    header("Location: manage_quiz.php");
    exit();
}

$id = intval($_GET['id']);

/* Fetch Quiz */
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    header("Location: manage_quiz.php");
    exit();
}

$quiz = $result->fetch_assoc();

/* UPDATE */
if(isset($_POST['update'])){

    $question = trim($_POST['question']);
    $option1 = trim($_POST['option1']);
    $option2 = trim($_POST['option2']);
    $option3 = trim($_POST['option3']);
    $option4 = trim($_POST['option4']);
    $correct = $_POST['correct_option'];

    $update = $conn->prepare("
        UPDATE quizzes
        SET question=?, option1=?, option2=?, option3=?, option4=?, correct_option=?
        WHERE id=?
    ");

    $update->bind_param(
        "ssssssi",
        $question,
        $option1,
        $option2,
        $option3,
        $option4,
        $correct,
        $id
    );

    if($update->execute()){
        header("Location: manage_quiz.php?updated=1");
    } else {
        echo "Error updating quiz";
    }

    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Quiz</title>

    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .form-container{
            max-width:650px;
            margin:40px auto;
            background: linear-gradient(145deg,#1e293b,#0f172a);
            padding:30px;
            border-radius:20px;
            box-shadow:0 10px 40px rgba(0,0,0,.6);
        }

        .form-container h2{
            text-align:center;
            margin-bottom:20px;
            color:white;
        }

        label{
            color:#cbd5e1;
            font-size:14px;
        }

        input, select{
            width:100%;
            padding:12px;
            margin:10px 0;
            border-radius:10px;
            border:1px solid rgba(255,255,255,0.05);
            background:#0f172a;
            color:white;
            outline:none;
        }

        input:focus, select:focus{
            border:1px solid #7c3aed;
            box-shadow:0 0 10px rgba(124,58,237,.4);
        }

        .btn{
            background:linear-gradient(90deg,#7c3aed,#6366f1);
            border:none;
            padding:12px;
            border-radius:12px;
            color:white;
            cursor:pointer;
            width:100%;
            margin-top:10px;
        }

        .btn:hover{
            opacity:.9;
        }
    </style>
</head>

<body>

<?php include "admin_sidebar.php"; ?>

<div class="main">

    <div class="form-container">

        <h2>Edit Quiz ✏️</h2>

        <form method="POST">

            <label>Question</label>
            <input type="text"
                   name="question"
                   value="<?php echo $quiz['question']; ?>"
                   required>

            <label>Option 1</label>
            <input type="text"
                   name="option1"
                   value="<?php echo $quiz['option1']; ?>"
                   required>

            <label>Option 2</label>
            <input type="text"
                   name="option2"
                   value="<?php echo $quiz['option2']; ?>"
                   required>

            <label>Option 3</label>
            <input type="text"
                   name="option3"
                   value="<?php echo $quiz['option3']; ?>"
                   required>

            <label>Option 4</label>
            <input type="text"
                   name="option4"
                   value="<?php echo $quiz['option4']; ?>"
                   required>

            <label>Correct Answer</label>
            <select name="correct_option">

                <option value="option1"
                    <?php if($quiz['correct_option']=="option1") echo "selected"; ?>>
                    Option 1
                </option>

                <option value="option2"
                    <?php if($quiz['correct_option']=="option2") echo "selected"; ?>>
                    Option 2
                </option>

                <option value="option3"
                    <?php if($quiz['correct_option']=="option3") echo "selected"; ?>>
                    Option 3
                </option>

                <option value="option4"
                    <?php if($quiz['correct_option']=="option4") echo "selected"; ?>>
                    Option 4
                </option>

            </select>

            <button class="btn" name="update">
                <i class="fa fa-save"></i> Update Quiz
            </button>

        </form>

    </div>

</div>

</body>
</html>