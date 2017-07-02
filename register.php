<?php
require_once 'config.php';

$username = $password = $confirm_password = "";

$username_err = $password_err = $confirm_password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(empty(trim($_POST["username"]))){

        $username_err = "Please enter a username.";

    } else{
        $sql = "SELECT id FROM users WHERE username = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        mysqli_stmt_close($stmt);

    }
    if(empty(trim($_POST['password']))){

        $password_err = "Please enter a password.";     

    } elseif(strlen(trim($_POST['password'])) < 5){
        $password_err = "Password must have atleast 5 characters.";
    } else{
        $password = trim($_POST['password']);
    }

    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = 'Please confirm password.';     
    } else{
        $confirm_password = trim($_POST['confirm_password']);
        if($password != $confirm_password){
            $confirm_password_err = 'Password did not match.';
        }

    }

    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){

        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

        if($stmt = mysqli_prepare($link, $sql)){

            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);

            $param_username = $username;

            $param_password = password_hash($password, PASSWORD_DEFAULT); 
            if(mysqli_stmt_execute($stmt)){
                header("location: login.php");
            } else{
                echo "Something went wrong. Please try again later.";
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
    <title>Sign Up</title>
    <style type="text/css">
        *{
            padding: 0;
            margin: 0;
        }
        div{
            width: 400px;
            height: 400px;
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
            margin: auto;
          
        
        }
    </style>
    <script type="text/javascript">
        function availability(string){
            if(string.length==0){
                document.getElementById("available").innerHTML="";
                return;
            }
            xmlhttp = new XMLHttpRequest();

            xmlhttp.onreadystatechange = function(){
                if(this.readyState==4 && this.status==200){
                    document.getElementById("available").innerHTML=this.responseText;
                    var text=this.responseText;
                    console.log(this.responseText.trim());
                    if(text.trim() == "Username unavailable"){
                        
                        document.getElementById("available").style.color = "red";
                    }else{
                       
                        document.getElementById("available").style.color = "green";
   
                    }
                }
            }
            xmlhttp.open("GET","livesearch.php?q="+string,true);
            xmlhttp.send();
        }
    </script>
</head>
<body>
    <div id="container">
        <h3>Sign Up</h3><br>
        <p>Please enter the folowing details to create an account</p><br>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" autocomplete="off">
            <label>Username<sup style="color: red">*</sup></label><br>
            <input type="text" name="username" value="<?php echo $username;?>" onkeyup="availability(this.value)">
            <span  id = "available" color="red"><?php echo $username_err;?></span>
            <br>
            <label>Password<sup style="color: red">*</sup></label><br>
            <input type="password" name="password" value="<?php echo $password;?>">
            <span style="color: red"><?php echo $password_err;?></span>
            <br>
            <label>Confrim Password<sup style="color: red">*</sup></label><br>
            <input type="password" name="confirm_password" value="<?php echo $confirm_password;?>">
            <span style="color: red"><?php echo $confirm_password_err;?></span>
            <br><br>
            <input type="submit" value="Submit">
            <input type="reset" value="Reset">
        </form><br>
        <a href="login.php">Already have an account?</a><br><br>
        <p><sup style="color: red">*</sup> required fields</p>
    </div>

</body>
</html>