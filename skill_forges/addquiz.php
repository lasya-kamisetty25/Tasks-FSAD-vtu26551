<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role']!='admin'){
    header("Location: login.php");
    exit();
}

$course_id = $_GET['course_id'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Quiz</title>

    <link rel="stylesheet" href="css/style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<?php include "admin_sidebar.php"; ?>

<div class="main">

    <!-- HEADER -->
    <div class="header">
        <h1>Add Quiz</h1>
        <p>Create quiz questions for course lessons.</p>
    </div>

    <!-- FORM -->
    <div class="form-container">

        <?php
        if(isset($_GET['success'])){
            echo "<div class='message'>✅ Quiz added successfully!</div>";
        }

        if(isset($_GET['error'])){
            echo "<div class='message'>❌ Error adding quiz</div>";
        }
        ?>

        <form method="POST" action="save_quiz.php">

            <!-- COURSE -->
            <label>Course</label>
            <select id="courseSelect" required>
                <option value="">Select Course</option>

                <?php
                $courses = mysqli_query($conn, "SELECT * FROM courses");

                while($c = mysqli_fetch_assoc($courses)){
                    $selected = ($course_id == $c['id']) ? "selected" : "";
                    echo "<option value='{$c['id']}' $selected>{$c['title']}</option>";
                }
                ?>
            </select>

            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

            <!-- LESSON -->
            <label>Lesson</label>
            <select name="lesson_id" required>

                <?php
                if($course_id > 0){
                    $lessons = mysqli_query($conn,
                        "SELECT * FROM lessons WHERE section_id='$course_id'"
                    );
                } else {
                    $lessons = mysqli_query($conn,
                        "SELECT * FROM lessons"
                    );
                }

                if(mysqli_num_rows($lessons) > 0){
                    while($l = mysqli_fetch_assoc($lessons)){
                        echo "<option value='{$l['id']}'>{$l['title']}</option>";
                    }
                } else {
                    echo "<option>No lessons found</option>";
                }
                ?>

            </select>

            <label>Quiz Question</label>
            <input type="text" name="question" required>

            <label>Option 1</label>
            <input type="text" name="option1" required>

            <label>Option 2</label>
            <input type="text" name="option2" required>

            <label>Option 3</label>
            <input type="text" name="option3" required>

            <label>Option 4</label>
            <input type="text" name="option4" required>

            <label>Correct Answer</label>
            <select name="correct_option" required>
                <option value="">Select Correct Answer</option>
                <option value="option1">Option 1</option>
                <option value="option2">Option 2</option>
                <option value="option3">Option 3</option>
                <option value="option4">Option 4</option>
            </select>

            <button type="submit">Save Quiz</button>

        </form>

    </div>

</div>

<script>
document.getElementById("courseSelect").addEventListener("change", function(){
    let courseId = this.value;

    if(courseId){
        window.location.href = "addquiz.php?course_id=" + courseId;
    }
});
</script>

</body>
</html>