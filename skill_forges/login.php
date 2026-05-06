<?php
session_start();
include "db.php";

$message = "";

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if(empty($email) || empty($password)){

        $message = "❌ Please fill all fields.";

    } else {

        $stmt = $conn->prepare("
            SELECT id, name, email, password, role
            FROM users
            WHERE email=?
        ");

        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){

            $user = $result->fetch_assoc();

            /* PASSWORD CHECK */
            if($password === $user['password']){

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                /* ROLE REDIRECT */
                if($user['role'] == "admin"){
                    header("Location: admin.php");
                } else {
                    header("Location: dashboard.php");
                }

                exit();

            } else {

                $message = "❌ Invalid password.";
            }

        } else {

            $message = "❌ User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - SkillForge</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins',sans-serif;
        }

        body{
            background:radial-gradient(circle at top, #1e1b4b, #020617);
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
            color:white;
        }

        .login-box{
            width:400px;
            background:linear-gradient(145deg,#1e293b,#0f172a);
            padding:35px;
            border-radius:20px;
            box-shadow:0 15px 40px rgba(0,0,0,.6);
        }

        .login-box h2{
            text-align:center;
            margin-bottom:25px;
        }

        .message{
            text-align:center;
            margin-bottom:15px;
            font-weight:bold;
        }

        input{
            width:100%;
            padding:12px;
            margin-bottom:15px;
            border:none;
            border-radius:10px;
            background:#334155;
            color:white;
        }

        button{
            width:100%;
            padding:12px;
            border:none;
            border-radius:10px;
            background:linear-gradient(90deg,#7c3aed,#6366f1);
            color:white;
            font-size:15px;
            cursor:pointer;
        }

        button:hover{
            opacity:.9;
        }

        .register-link{
            margin-top:15px;
            text-align:center;
        }

        .register-link a{
            color:#7c3aed;
            text-decoration:none;
        }
    </style>
</head>

<body>

<div class="login-box">

    <h2>Login to SkillForge</h2>

    <?php if(!empty($message)){ ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <form method="POST">

        <input type="email"
               name="email"
               placeholder="Email Address"
               required>

        <input type="password"
               name="password"
               placeholder="Password"
               required>

        <button type="submit" name="login">
            Login
        </button>

    </form>

    <div class="register-link">
        Don't have an account?
        <a href="register.php">Register</a>
    </div>

</div>

</body>
</html>