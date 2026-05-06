<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM courses";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Courses</title>
    <link rel="stylesheet" href="css/style.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* GRID */
        .course-grid{
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
            gap:25px;
            margin-top:30px;
        }

        /* CARD */
        .course-card{
            background: linear-gradient(145deg,#1e293b,#0f172a);
            border-radius:18px;
            padding:15px;
            box-shadow:0 10px 30px rgba(0,0,0,.6);
            display:flex;
            flex-direction:column;
            height:100%;
        }

        /* IMAGE */
        .course-img{
            width:100%;
            height:170px;
            object-fit:cover;
            border-radius:12px;
            margin-bottom:10px;
        }

        /* BADGE */
        .badge{
            background:linear-gradient(90deg,#7c3aed,#6366f1);
            padding:6px 12px;
            border-radius:8px;
            font-size:12px;
            display:inline-block;
            width:max-content;
            margin-bottom:8px;
        }

        /* TITLE */
        .course-card h3{
            font-size:18px;
            margin-bottom:8px;
        }

        /* DESCRIPTION */
        .desc{
            font-size:14px;
            color:#cbd5e1;
            line-height:1.5;
            margin-bottom:10px;
            display:-webkit-box;
            -webkit-line-clamp:3;
            -webkit-box-orient:vertical;
            overflow:hidden;
        }

        /* PRICE */
        .price{
            color:#22c55e;
            font-weight:600;
            margin-bottom:10px;
        }

        /* BUTTON CONTAINER */
        .btn-group{
            margin-top:auto;
            display:flex;
            gap:10px;
        }

        /* BUTTON */
        .btn{
            flex:1;
            padding:10px;
            font-size:14px;
            background:linear-gradient(90deg,#7c3aed,#6366f1);
            border:none;
            border-radius:10px;
            color:white;
            cursor:pointer;
            text-align:center;
            text-decoration:none;
            transition:.3s;
        }

        .btn:hover{
            transform:scale(1.05);
        }
    </style>
</head>

<body>

<?php include "admin_sidebar.php"; ?>

<div class="main">

    <div class="header">
        <h1>Manage Courses</h1>
        <p>Add, edit or delete courses.</p>
    </div>

    <div style="margin-bottom:20px;">
        <a href="addcourse.php" class="btn">
            <i class="fa fa-plus"></i> Add New Course
        </a>
    </div>

    <div class="course-grid">

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                $imagePath = !empty($row['image']) && file_exists("uploads/" . $row['image'])
                    ? "uploads/" . $row['image']
                    : "https://via.placeholder.com/400x200?text=Course";

                /* SHORT DESCRIPTION */
                $shortDesc = substr($row['description'], 0, 100) . "...";
        ?>

        <div class="course-card">

            <img src="<?php echo $imagePath; ?>" class="course-img">

            <span class="badge">
                <?php echo $row['category']; ?>
            </span>

            <h3>
                <i class="fa fa-book-open"></i>
                <?php echo $row['title']; ?>
            </h3>

            <p class="desc">
                <?php echo $shortDesc; ?>
            </p>

            <div class="price">
                ₹ <?php echo $row['price']; ?>
            </div>

            <div class="btn-group">

                <a href="editcourse.php?id=<?php echo $row['id']; ?>" class="btn">
                    <i class="fa fa-edit"></i> Edit
                </a>

                <a href="deletecourse.php?id=<?php echo $row['id']; ?>"
                   class="btn"
                   onclick="return confirm('Are you sure to delete this course?')">
                    <i class="fa fa-trash"></i> Delete
                </a>

            </div>

        </div>

        <?php
            }
        } else {
            echo "<h3>No courses available.</h3>";
        }
        ?>

    </div>

</div>

</body>
</html>