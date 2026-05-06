<?php
error_reporting(0);
ini_set('display_errors', 0);
ini_set('upload_max_filesize', '500M');
ini_set('post_max_size', '500M');
ini_set('max_execution_time', '300');
ini_set('max_input_time', '300');
ini_set('memory_limit', '512M');

error_reporting(0);
ini_set('display_errors', 0);

session_start();
include "db.php";

/* ADMIN PROTECTION */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

$message = "";

if(isset($_POST['add'])){

    $course_id = intval($_POST['course_id']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $video_file = "";

    if(empty($course_id) || empty($title) || empty($content)){

        $message = "❌ Please fill all required fields.";

    } else {

        /* VIDEO UPLOAD */
        if(isset($_FILES['video']) && $_FILES['video']['error'] == 0){

            $maxSize = 500 * 1024 * 1024; // 500MB max

            if($_FILES['video']['size'] > $maxSize){

                $message = "❌ Video too large. Maximum allowed size is 500MB.";

            } else {

                $allowedTypes = ['video/mp4','video/webm','video/ogg'];
                $fileType = $_FILES['video']['type'];

                if(in_array($fileType, $allowedTypes)){

                    /* CREATE VIDEOS FOLDER */
                    if(!is_dir("videos")){
                        mkdir("videos", 0777, true);
                    }

                    $videoName = time() . "_" . basename($_FILES['video']['name']);
                    $uploadPath = "videos/" . $videoName;

                    if(move_uploaded_file($_FILES['video']['tmp_name'], $uploadPath)){

                        $video_file = $videoName;

                    } else {

                        $message = "❌ Failed to upload video.";
                    }

                } else {

                    $message = "❌ Only MP4, WEBM, or OGG videos allowed.";
                }
            }

        } else {

            $message = "❌ Please upload a lesson video.";
        }

        /* INSERT LESSON */
        if(empty($message)){

            $stmt = $conn->prepare("
                INSERT INTO lessons
                (section_id, title, content, video_url)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "isss",
                $course_id,
                $title,
                $content,
                $video_file
            );

            if($stmt->execute()){

                $message = "✅ Lesson added successfully!";

            } else {

                $message = "❌ Database error while adding lesson.";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Lesson</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

</head>

<body>

<?php include "admin_sidebar.php"; ?>

<div class="main">

    <!-- HEADER -->
    <div class="header">
        <h1>Add Lesson</h1>
        <p>Create and manage course lessons efficiently.</p>
    </div>

    <!-- MESSAGE -->
    <?php if(!empty($message)){ ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <!-- FORM -->
    <div class="form-container">

        <form method="POST" enctype="multipart/form-data">

            <!-- COURSE -->
            <label>Select Course</label>
            <select name="course_id" required>

                <option value="">Choose Course</option>

                <?php
                $courses = mysqli_query($conn, "SELECT * FROM courses");

                while($course = mysqli_fetch_assoc($courses)){
                    echo "<option value='{$course['id']}'>" . htmlspecialchars($course['title']) . "</option>";
                }
                ?>

            </select>

            <!-- TITLE -->
            <label>Lesson Title</label>
            <input type="text"
                   name="title"
                   placeholder="Enter lesson title"
                   required>

            <!-- CONTENT -->
            <label>Lesson Content</label>
            <textarea name="content"
                      placeholder="Enter lesson content"
                      required></textarea>

            <!-- VIDEO FILE -->
            <label>Upload Lesson Video</label>
            <input type="file"
                   name="video"
                   accept=".mp4,.webm,.ogg"
                   required>

            <!-- SUBMIT -->
            <button type="submit" class="btn" name="add">
                Add Lesson
            </button>

        </form>

    </div>

</div>

</body>
</html>