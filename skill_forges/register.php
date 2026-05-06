<?php
session_start();
include "db.php";

$message = "";

if(isset($_POST['register'])){

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = "user";

    if(empty($name) || empty($email) || empty($password)){

        $message = "❌ Please fill all fields.";

    } else {

        /* CHECK EXISTING USER */
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s",$email);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0){

            $message = "⚠️ Email already registered.";

        } else {

            /* INSERT USER */
            $stmt = $conn->prepare("
                INSERT INTO users(name,email,password,role)
                VALUES(?,?,?,?)
            ");

            $stmt->bind_param(
                "ssss",
                $name,
                $email,
                $password,
                $role
            );

            if($stmt->execute()){

                $message = "✅ Registration successful! Please login.";

            } else {

                $message = "❌ Registration failed.";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - SkillForge</title>

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

        .register-box{
            width:420px;
            background:linear-gradient(145deg,#1e293b,#0f172a);
            padding:35px;
            border-radius:20px;
            box-shadow:0 15px 40px rgba(0,0,0,.6);
        }

        .register-box h2{
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

        .login-link{
            margin-top:15px;
            text-align:center;
        }

        .login-link a{
            color:#7c3aed;
            text-decoration:none;
        }
    </style>
</head>

<body>

<div class="register-box">

    <h2>Create SkillForge Account</h2>

    <?php if(!empty($message)){ ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <form method="POST">

        <input type="text"
               name="name"
               placeholder="Full Name"
               required>

        <input type="email"
               name="email"
               placeholder="Email Address"
               required>

        <input type="password"
               name="password"
               placeholder="Password"
               required>

        <button type="submit" name="register">
            Register
        </button>

    </form>

    <div class="login-link">
        Already have an account?
        <a href="login.php">Login</a>
    </div>

</div>

</body>
</html>