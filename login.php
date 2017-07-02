<?php

require_once 'config.php';
$username = $password = "";
$username_err = $password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(empty(trim($_POST["username"]))){
        $username_err = 'Please enter username.';
    } else{
        $username = trim($_POST["username"]);
    }
    if(empty(trim($_POST['password']))){
        $password_err = 'Please enter your password.';
    } else{
        $password = trim($_POST['password']);
    }

 
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT username, password FROM users WHERE username = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
 
                if(mysqli_stmt_num_rows($stmt) == 1){                    

                    mysqli_stmt_bind_result($stmt, $username, $hashed_password);

                    if(mysqli_stmt_fetch($stmt)){

                        if(password_verify($password, $hashed_password)){
                            session_start();
                            $_SESSION['username'] = $username;      
                            header("location: welcome.php");
                        } else{
                            $password_err = 'The password you entered was not valid.';
                        }
                    }
                } else{
                    $username_err = 'No account found with that username.';
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
    
</head>
    <style type="text/css">
        *{
            padding: 0;
            margin: 0;
        }
        div{
            width: 450px;
            height: 400px;
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
            margin: auto;
        }
    </style>
<body>
    <div id="container">
        <h3>Login</h3><br>
        <p>Please enter your credentials</p><br>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label>Username<sup style="color: red">*</sup></label><br>
            <input type="text" name="username" value="<?php echo $username;?>" >
            <span style="color:red"><?php echo $username_err;?></span>
            <br>
            <label>Password<sup style="color: red">*</sup></label><br>
            <input type="password" name="password" value="<?php echo $password;?>">
            <span style="color: red"><?php echo $password_err;?></span>
            <br><br>
            <input type="submit" value="Submit">
        </form><br>
        <a href="register.php" style="margin-right: 30px">Don't have an account?</a>
        <a href="welcome.php">Continue as guest</a><br><br>
        <p><sup style="color: red">*</sup> required fields</p>
    </div>
</body>
</html>