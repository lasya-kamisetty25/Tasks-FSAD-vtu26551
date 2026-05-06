<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role']!='admin'){
    header("Location: login.php");
    exit();
}

/* QUIZ DATA */
$query = "
    SELECT
        quizzes.id,
        quizzes.question,
        lessons.title AS lesson_title
    FROM quizzes
    LEFT JOIN lessons ON quizzes.lesson_id = lessons.id
    ORDER BY quizzes.id DESC
    LIMIT 30
";

$quiz = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Quiz</title>

    <link rel="stylesheet" href="css/style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<?php include "admin_sidebar.php"; ?>

<div class="main">

    <!-- HEADER -->
    <div class="header">
        <h1>Manage Quiz</h1>
        <p>Edit or delete quiz questions easily.</p>
    </div>

    <!-- TABLE -->
    <div class="table-box">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Lesson</th>
                    <th>Question</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php
                if(mysqli_num_rows($quiz) > 0){

                    while($q = mysqli_fetch_assoc($quiz)){
                ?>

                <tr>

                    <td><?php echo $q['id']; ?></td>

                    <td><?php echo htmlspecialchars($q['lesson_title']); ?></td>

                    <td><?php echo htmlspecialchars($q['question']); ?></td>

                    <td>

                        <a href="edit_quiz.php?id=<?php echo $q['id']; ?>"
                           class="btn">
                            Edit
                        </a>

                        <button class="btn"
                                onclick="deleteQuiz(<?php echo $q['id']; ?>)">
                            Delete
                        </button>

                    </td>

                </tr>

                <?php
                    }

                } else {
                ?>

                <tr>
                    <td colspan="4" style="text-align:center;">
                        No quiz questions found.
                    </td>
                </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</div>

<script>
function deleteQuiz(id){

    if(confirm("Delete this quiz?")){

        fetch("delete_quiz.php?id=" + id)
            .then(response => response.text())
            .then(data => {
                location.reload();
            })
            .catch(error => {
                alert("Error deleting quiz.");
            });
    }
}
</script>

</body>
</html>