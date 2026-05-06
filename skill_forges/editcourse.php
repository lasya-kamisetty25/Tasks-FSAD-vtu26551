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

if(!isset($_GET['id'])){
    header("Location: managecourses.php");
    exit();
}

$id = intval($_GET['id']);

/* Fetch Course */
$stmt = $conn->prepare("SELECT * FROM courses WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    header("Location: managecourses.php");
    exit();
}

$course = $result->fetch_assoc();

/* UPDATE */
if(isset($_POST['update'])){

    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $cat = trim($_POST['category']);
    $price = intval($_POST['price']);
    $course_level = $_POST['level'];
    $duration = intval($_POST['duration']);
    $instructor = trim($_POST['instructor']);
    $tags = trim($_POST['tags']);
    $status = $_POST['status'];

    $image = $course['image'];

    /* Image Upload */
    if(!empty($_FILES['image']['name'])){

        $allowedTypes = ['image/jpeg','image/png','image/webp'];
        $fileType = $_FILES['image']['type'];

        if(in_array($fileType,$allowedTypes)){

            $imageName = time()."_".basename($_FILES["image"]["name"]);
            $targetFile = "uploads/".$imageName;

            if(move_uploaded_file($_FILES["image"]["tmp_name"],$targetFile)){
                $image = $imageName;
            }
        }
    }

    /* UPDATE QUERY */
    $update = $conn->prepare("
        UPDATE courses
        SET title=?, description=?, category=?, price=?, image=?, course_level=?, duration=?, instructor=?, tags=?, status=?
        WHERE id=?
    ");

    $update->bind_param(
        "sssississsi",
        $title,
        $desc,
        $cat,
        $price,
        $image,
        $course_level,
        $duration,
        $instructor,
        $tags,
        $status,
        $id
    );

    $update->execute();

    header("Location: managecourses.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Course</title>

    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .form-box{
            max-width:600px;
            margin:auto;
            margin-top:30px;
        }

        .form-box input,
        .form-box textarea,
        .form-box select{
            width:100%;
            padding:12px;
            margin:10px 0;
            border:none;
            border-radius:10px;
        }

        .form-box textarea{
            resize:none;
            height:100px;
        }
    </style>
</head>

<body>

<?php include "admin_sidebar.php"; ?>

<div class="main">

    <div class="header">
        <h1>Edit Course ✏️</h1>
        <p>Update course information</p>
    </div>

    <div class="form-box">

        <form method="POST" enctype="multipart/form-data">

            <label>Course Title</label>
            <input type="text"
                   name="title"
                   value="<?php echo $course['title'];?>"
                   required>

            <label>Description</label>
            <textarea name="description" required><?php echo $course['description'];?></textarea>

            <label>Category</label>
            <input type="text"
                   name="category"
                   value="<?php echo $course['category'];?>"
                   required>

            <label>Course Level</label>
            <select name="level">
                <option <?php if($course['course_level']=="Beginner") echo "selected"; ?>>
                    Beginner
                </option>
                <option <?php if($course['course_level']=="Intermediate") echo "selected"; ?>>
                    Intermediate
                </option>
                <option <?php if($course['course_level']=="Advanced") echo "selected"; ?>>
                    Advanced
                </option>
            </select>

            <label>Duration (hours)</label>
            <input type="number"
                   name="duration"
                   value="<?php echo $course['duration'];?>">

            <label>Instructor</label>
            <input type="text"
                   name="instructor"
                   value="<?php echo $course['instructor'];?>">

            <label>Tags</label>
            <input type="text"
                   name="tags"
                   value="<?php echo $course['tags'];?>">

            <label>Status</label>
            <select name="status">
                <option <?php if($course['status']=="Active") echo "selected"; ?>>
                    Active
                </option>
                <option <?php if($course['status']=="Draft") echo "selected"; ?>>
                    Draft
                </option>
            </select>

            <label>Price</label>
            <input type="number"
                   name="price"
                   value="<?php echo $course['price'];?>"
                   required>

            <label>Current Image</label><br>

            <img src="uploads/<?php echo $course['image'];?>"
                 style="width:200px;height:120px;object-fit:cover;border-radius:10px;"><br><br>

            <label>Change Image</label>
            <input type="file" name="image">

            <button class="btn" name="update">
                <i class="fa fa-save"></i> Update Course
            </button>

        </form>

    </div>

</div>

</body>
</html>