<?php
session_start();
include "db.php";

/* ADMIN PROTECTION */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

/* FETCH ALL LESSONS WITH COURSE */
$query = "
    SELECT lessons.*, courses.title AS course_title
    FROM lessons
    JOIN courses ON lessons.section_id = courses.id
    ORDER BY lessons.id DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Lessons</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>

<?php include "admin_sidebar.php"; ?>

<div class="main">

    <!-- HEADER -->
    <div class="header">
        <h1>Manage Lessons</h1>
        <p>Edit or remove course lessons easily.</p>
    </div>

    <!-- TABLE -->
    <div class="table-box">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course</th>
                    <th>Lesson Title</th>
                    <th>Video</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php
                if(mysqli_num_rows($result) > 0){

                    while($lesson = mysqli_fetch_assoc($result)){
                ?>

                <tr>

                    <!-- ID -->
                    <td><?php echo $lesson['id']; ?></td>

                    <!-- COURSE -->
                    <td><?php echo htmlspecialchars($lesson['course_title']); ?></td>

                    <!-- TITLE -->
                    <td><?php echo htmlspecialchars($lesson['title']); ?></td>

                    <!-- VIDEO -->
                    <td>
                        <?php
                        if(!empty($lesson['video_url']) && file_exists(__DIR__ . "/videos/" . $lesson['video_url'])){
                        ?>
                            <a href="./videos/<?php echo htmlspecialchars($lesson['video_url']); ?>"
                               target="_blank"
                               class="btn">
                                View Video
                            </a>
                        <?php
                        } else {
                            echo "No Video";
                        }
                        ?>
                    </td>

                    <!-- ACTIONS -->
                    <td>

                        <!-- EDIT -->
                        <a href="edit_lesson.php?id=<?php echo $lesson['id']; ?>"
                           class="btn">
                            Edit
                        </a>

                        <!-- DELETE -->
                        <a href="delete_lesson.php?id=<?php echo $lesson['id']; ?>"
                           class="btn"
                           onclick="return confirm('Delete this lesson?');">
                            Delete
                        </a>

                    </td>

                </tr>

                <?php
                    }

                } else {
                ?>

                <tr>
                    <td colspan="5" style="text-align:center;">
                        No lessons available.
                    </td>
                </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>