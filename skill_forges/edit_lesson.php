<?php
session_start();
include "db.php";

/* ADMIN PROTECTION */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])){
    header("Location: manage_lessons.php");
    exit();
}

$lesson_id = intval($_GET['id']);
$message = "";

/* FETCH LESSON */
$stmt = $conn->prepare("SELECT * FROM lessons WHERE id=?");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    header("Location: manage_lessons.php");
    exit();
}

$lesson = $result->fetch_assoc();

/* UPDATE LESSON */
if(isset($_POST['update'])){

    $course_id = intval($_POST['course_id']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $video_file = $lesson['video_url'];

    if(empty($course_id) || empty($title) || empty($content)){

        $message = "❌ Please fill all required fields.";

    } else {

        /* NEW VIDEO UPLOAD */
        if(isset($_FILES['video']) && $_FILES['video']['error'] == 0){

            $allowedTypes = ['video/mp4','video/webm','video/ogg'];
            $fileType = $_FILES['video']['type'];

            if(in_array($fileType, $allowedTypes)){

                if(!is_dir("videos")){
                    mkdir("videos",0777,true);
                }

                $videoName = time() . "_" . basename($_FILES['video']['name']);
                $uploadPath = "videos/" . $videoName;

                if(move_uploaded_file($_FILES['video']['tmp_name'], $uploadPath)){

                    /* DELETE OLD VIDEO */
                    if(!empty($lesson['video_url']) && file_exists("videos/".$lesson['video_url'])){
                        unlink("videos/".$lesson['video_url']);
                    }

                    $video_file = $videoName;

                } else {

                    $message = "❌ Failed to upload new video.";
                }

            } else {

                $message = "❌ Only MP4, WEBM, or OGG allowed.";
            }
        }

        /* UPDATE DB */
        if(empty($message)){

            $update = $conn->prepare("
                UPDATE lessons
                SET section_id=?, title=?, content=?, video_url=?
                WHERE id=?
            ");

            $update->bind_param(
                "isssi",
                $course_id,
                $title,
                $content,
                $video_file,
                $lesson_id
            );

            if($update->execute()){

                $message = "✅ Lesson updated successfully!";

                /* REFRESH DATA */
                header("Refresh:1; url=manage_lessons.php");

            } else {

                $message = "❌ Failed to update lesson.";
            }

            $update->close();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Lesson</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body>

<?php include "admin_sidebar.php"; ?>

<div class="main">

    <div class="header">
        <h1>Edit Lesson</h1>
        <p>Update lesson details and video content.</p>
    </div>

    <?php if(!empty($message)){ ?>
        <div class="message"><?php echo $message; ?></div>
    <?php } ?>

    <div class="form-container">

        <form method="POST" enctype="multipart/form-data">

            <!-- COURSE -->
            <label>Select Course</label>
            <select name="course_id" required>

                <?php
                $courses = mysqli_query($conn, "SELECT * FROM courses");

                while($course = mysqli_fetch_assoc($courses)){
                    $selected = ($course['id'] == $lesson['section_id']) ? "selected" : "";
                    echo "<option value='{$course['id']}' $selected>" .
                         htmlspecialchars($course['title']) .
                         "</option>";
                }
                ?>

            </select>

            <!-- TITLE -->
            <label>Lesson Title</label>
            <input type="text"
                   name="title"
                   value="<?php echo htmlspecialchars($lesson['title']); ?>"
                   required>

            <!-- CONTENT -->
            <label>Lesson Content</label>
            <textarea name="content" required><?php echo htmlspecialchars($lesson['content']); ?></textarea>

            <!-- CURRENT VIDEO -->
            <?php if(!empty($lesson['video_url']) && file_exists("videos/".$lesson['video_url'])){ ?>
                <label>Current Video</label>
                <a href="videos/<?php echo $lesson['video_url']; ?>" target="_blank" class="btn">
                    View Current Video
                </a>
            <?php } ?>

            <!-- NEW VIDEO -->
            <label>Upload New Video (Optional)</label>
            <input type="file"
                   name="video"
                   accept=".mp4,.webm,.ogg">

            <!-- SUBMIT -->
            <button type="submit" class="btn" name="update">
                Update Lesson
            </button>

        </form>

    </div>

</div>

</body>
</html>