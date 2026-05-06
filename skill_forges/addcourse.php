<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role']!='admin'){
    header("Location: login.php");
    exit();
}

$message = "";

if(isset($_POST['add'])){

    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $cat = trim($_POST['category']);
    $price = intval($_POST['price']);
    $course_level = $_POST['level'];
    $duration = intval($_POST['duration']);
    $instructor = trim($_POST['instructor']);
    $tags = trim($_POST['tags']);
    $status = $_POST['status'];

    if(empty($title) || empty($desc) || empty($cat) || $price <= 0){

        $message = "❌ Please fill all required fields.";

    } else {

        /* CHECK DUPLICATE */
        $check = $conn->prepare("SELECT id FROM courses WHERE title=?");
        $check->bind_param("s",$title);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0){

            $message = "⚠️ Course already exists!";

        } else {

            /* IMAGE UPLOAD */
            if(isset($_FILES['image']) && $_FILES['image']['error']==0){

                $allowedTypes = ['image/jpeg','image/png','image/webp'];
                $fileType = $_FILES['image']['type'];

                if(in_array($fileType,$allowedTypes)){

                    if(!is_dir("uploads")){
                        mkdir("uploads",0777,true);
                    }

                    $imageName = time()."_".basename($_FILES['image']['name']);
                    $uploadPath = "uploads/".$imageName;

                    if(move_uploaded_file($_FILES['image']['tmp_name'],$uploadPath)){

                        $stmt = $conn->prepare("
                            INSERT INTO courses
                            (title,description,category,price,image,course_level,duration,instructor,tags,status)
                            VALUES(?,?,?,?,?,?,?,?,?,?)
                        ");

                        $stmt->bind_param(
                            "sssississs",
                            $title,
                            $desc,
                            $cat,
                            $price,
                            $imageName,
                            $course_level,
                            $duration,
                            $instructor,
                            $tags,
                            $status
                        );

                        if($stmt->execute()){
                            $message = "✅ Course added successfully!";
                        } else {
                            $message = "❌ Database error: ".$stmt->error;
                        }

                        $stmt->close();

                    } else {
                        $message = "❌ Failed to upload image.";
                    }

                } else {
                    $message = "❌ Only JPG, PNG or WEBP allowed.";
                }

            } else {
                $message = "❌ Please select an image.";
            }
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Course</title>

    <link rel="stylesheet" href="css/style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body>

<?php include "admin_sidebar.php"; ?>

<div class="main">

    <!-- HEADER -->
    <div class="header">
        <h1>Add Course</h1>
        <p>Create and publish new learning programs.</p>
    </div>

    <!-- MESSAGE -->
    <?php if(!empty($message)){ ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <!-- FORM -->
    <div class="form-container add-course-form">

        <form method="POST" enctype="multipart/form-data">

            <input type="text"
                   name="title"
                   placeholder="Course Title"
                   required>

            <textarea name="description"
                      placeholder="Description"
                      required></textarea>

            <select name="category" required>
                <option value="">Category</option>
                <option>Programming</option>
                <option>Web Development</option>
                <option>Data Science</option>
                <option>AI</option>
            </select>

            <select name="level">
                <option value="">Level</option>
                <option>Beginner</option>
                <option>Intermediate</option>
                <option>Advanced</option>
            </select>

            <input type="number"
                   name="duration"
                   placeholder="Duration (hours)">

            <input type="text"
                   name="instructor"
                   placeholder="Instructor">

            <input type="text"
                   name="tags"
                   placeholder="Tags">

            <select name="status">
                <option>Active</option>
                <option>Draft</option>
            </select>

            <input type="number"
                   name="price"
                   placeholder="Price"
                   required>

            <input type="file"
                   name="image"
                   accept=".jpg,.jpeg,.png,.webp"
                   required>

            <button type="submit" class="btn" name="add">
                Add Course
            </button>

        </form>

    </div>

</div>

</body>
</html>