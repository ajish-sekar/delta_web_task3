<?php
require_once 'config.php';
session_start();
$code = $created_by="";
$name = $_SESSION['code_name'];
$sql = "SELECT snippet, created_by FROM code WHERE name = ?";
if($stmt = mysqli_prepare($link,$sql)){
	mysqli_stmt_bind_param($stmt,"s",$param_name);
	$param_name=$name;
	if(mysqli_stmt_execute($stmt)){
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt,$param_code,$param_creator);
		mysqli_stmt_fetch($stmt);
		$code=$param_code;
		$created_by=$param_creator;
	}
}
mysqli_stmt_close($stmt);

$code_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    

    
    if(isset($_POST['code'])){
    if(empty(trim($_POST['code']))){
        $code_err = "Please enter the code.";     

    } 
     else{
        $code = trim($_POST['code']);
    }

    
    

    

    if(empty($code_err)){

        $sql = "UPDATE code SET snippet=? where name= ?";

        if($stmt = mysqli_prepare($link, $sql)){

            mysqli_stmt_bind_param($stmt, "ss", $param_snippet, $param_name);

            $param_snippet = $code;

            $param_name =$name;
            
            if(mysqli_stmt_execute($stmt)){
                header("location: welcome.php");
            } else{
                echo "Something went wrong. Please try again later. insert";
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
}
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Code</title>
    <style type="text/css">
        *{
            padding: 0;
            margin: 0;
        }

        textarea{
        	white-space: pre-wrap;
        }
        
    </style>
</head>
<body>
	<?php 
		
		if(!isset($_SESSION['username'])||$_SESSION['username']!=$created_by){
			echo "<script>
					alert(\"Please Login to add code\");
					document.location.href=\"login.php\";
					</script>";

			
		}
	 ?>
    <div id="container">
        <h3>Edit code</h3><br>
        <p><?php echo $name;  ?></p><br>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            
            <label>Code<sup style="color: red">*</sup></label><br>
            <textarea name="code" rows="30" cols="70"><?php echo $code;?></textarea>
            <span style="color: red"><?php echo $code_err;?></span>
            <br><br>
            <input type="submit" value="Save">
            <input type="reset" value="Reset">
        </form><br>
        <a href="welcome.php">Go back</a><br><br>
        <p><sup style="color: red">*</sup> required fields</p>
    </div>

</body>
</html>