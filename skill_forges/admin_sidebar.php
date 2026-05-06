<div class="sidebar">

    <div class="logo">Admin Panel</div>

    <?php
    $current = basename($_SERVER['PHP_SELF']);
    ?>

    <a href="admin.php"
       class="<?= ($current=='admin.php') ? 'active' : ''; ?>">
        <i class="fa fa-chart-line"></i> Dashboard
    </a>

    <a href="addcourse.php"
       class="<?= ($current=='addcourse.php') ? 'active' : ''; ?>">
        <i class="fa fa-plus"></i> Add Course
    </a>

    <a href="managecourses.php"
       class="<?= ($current=='managecourses.php' || $current=='editcourse.php') ? 'active' : ''; ?>">
        <i class="fa fa-layer-group"></i> Manage Courses
    </a>

    <a href="add_lesson.php"
       class="<?= ($current=='add_lesson.php') ? 'active' : ''; ?>">
        <i class="fa fa-play"></i> Add Lesson
    </a>

    <a href="manage_lessons.php"
       class="<?= ($current=='manage_lessons.php' || $current=='edit_lesson.php') ? 'active' : ''; ?>">
        <i class="fa fa-book-open"></i> Manage Lessons
    </a>

    <a href="addquiz.php"
       class="<?= ($current=='addquiz.php') ? 'active' : ''; ?>">
        <i class="fa fa-question-circle"></i> Add Quiz
    </a>

    <a href="manage_quiz.php"
       class="<?= ($current=='manage_quiz.php' || $current=='edit_quiz.php') ? 'active' : ''; ?>">
        <i class="fa fa-list"></i> Manage Quiz
    </a>

    <a href="dashboard.php">
        <i class="fa fa-user"></i> User Dashboard
    </a>

    <a href="logout.php">
        <i class="fa fa-sign-out-alt"></i> Logout
    </a>

</div>