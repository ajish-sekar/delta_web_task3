<?php
require_once 'config.php';
session_start();
$name = $code = $created_by = $access = "";
$anonymous=0;
$name_err = $code_err = $upload_err ="";

if($_SERVER["REQUEST_METHOD"] == "POST"){

	if(isset($_POST['name'])){
    if(empty(trim($_POST["name"]))){

        $name_err = "Please enter a name.";

    }else{
        $sql = "SELECT id FROM code WHERE name = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_name);
            $param_name = trim($_POST["name"]);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $name_err = "This name is already taken.";
                } else{
                    $name = trim($_POST["name"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later. select";
            }
        }
        mysqli_stmt_close($stmt);

    }
    if(empty(trim($_POST['code']))){
        $code_err = "Please enter the code.";     

    } 
     else{
        $code = trim($_POST['code']);
    }
    if(isset($_POST['anonymous'])){
    if ($_POST["anonymous"]==true) {
    	$anonymous = 1;
    }else{
    	$anonymous = 0;
    }
    }else{
    	$anonymous=0;
    }
    $created_by = $_SESSION['username'];
    $access = $_POST['access'];
    $language=$_POST['language'];

    

    if(empty($name_err) && empty($code_err)){

        $sql = "INSERT INTO code (snippet, name, created_by, access, anonymous, language) VALUES (?, ?, ?, ?, ?, ?)";

        if($stmt = mysqli_prepare($link, $sql)){

            mysqli_stmt_bind_param($stmt, "ssssis", $param_snippet, $param_name, $param_creator, $param_access, $param_anonymous, $param_language);

            $param_snippet = $code;

            $param_name =$name;
            $param_creator = $created_by;
            $param_access = $access;
            $param_anonymous = $anonymous;
            $param_language=$language; 
            if(mysqli_stmt_execute($stmt)){
                header("location: welcome.php");
            } else{
                echo "Something went wrong. Please try again later. insert";
            }
        }
        mysqli_stmt_close($stmt);
    }
}
	if(isset($_POST['submit2'])){
		$target_dir = "uploads/";
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		$uploadOk = 1;
		$codeFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		
		
		
		if (file_exists($target_file)) {
		   $upload_err="Sorry, file already exists.";
		    $uploadOk = 0;
		}
		
		
		if($codeFileType != "markup" && $codeFileType != "css" && $codeFileType != "clike"
		&& $codeFileType != "javascript" && $codeFileType != "c" && $codeFileType != "cpp" && $codeFileType != "git"
		&& $codeFileType != "http" && $codeFileType != "java" && $codeFileType != "json" && $codeFileType != "php" && $codeFileType != "python" && $codeFileType != "sql") {
		    $upload_err= "Sorry, only markup, css, clike, javascript, c, cpp, git, http, java, json, php, python & sql files are allowed.";
		    $uploadOk = 0;
		}
		if($uploadOk==1) {
		    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
		        $code=file_get_contents($target_file);
		        $name = basename($target_file,".".$codeFileType);
		    } else {
		        $upload_err= "Sorry, there was an error uploading your file.";
		    }
		}

	}
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Add code</title>
    <style type="text/css">
        *{
            padding: 0;
            margin: 0;
        }

        textarea{
        	white-space: pre-wrap;
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
                    if(text.trim() == "Name unavailable"){
                        
                        document.getElementById("available").style.color = "red";
                    }else{
                       
                        document.getElementById("available").style.color = "green";
   
                    }
                }
            }
            xmlhttp.open("GET","livesearch2.php?q="+string,true);
            xmlhttp.send();
        }
    </script>
</head>
<body>
	<?php 
		
		if(!isset($_SESSION['username'])||$_SESSION['username']=="Guest"){
			echo "<script>
					alert(\"Please Login to add code\");
					document.location.href=\"login.php\";
					</script>";

			
		}
	 ?>
    <div id="container">
        <h3>Add code</h3><br>
        <p>Please enter the folowing details to add the code</p><br>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
		    <label>Select file to upload:</label>
		    <input type="file" name="fileToUpload" id="fileToUpload">
		    <input type="submit" value="Upload Code" name="submit2">
		    <span style="color: red"><?php echo $upload_err;  ?></span>
		</form>
		<br>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" autocomplete="off">
            <label>Name<sup style="color: red">*</sup></label><br>
            <input type="text" name="name" value="<?php echo $name;?>" onkeyup="availability(this.value)">
            <span style="color:red" id="available"><?php echo $name_err;?></span>
            <br><br>
            <label>Make the code :</label>
            <input type="radio" name="access" value="Public" checked>Public
            <input type="radio" name="access" value="Private">Private
            <br><br>
            <label>Make the code anonymous?</label>
            <input type="checkbox" name="anonymous" value="true">
            <br><br>
            <label>Choose Language</label>
            <select name="language" id="lang">
            	<option value="markup">markup</option>
            	<option value="css" selected>css</option>
            	<option value="clike">clike</option>
            	<option value="javascript">javascript</option>
            	<option value="c">c</option>
            	<option value="cpp">cpp</option>
            	<option value="git">git</option>
            	<option value="http">http</option>
            	<option value="java">java</option>
            	<option value="json">json</option>
            	<option value="php">php</option>
            	<option value="python">python</option>
            	<option value="sql">sql</option>
            </select><br><br>
            <label>Code<sup style="color: red">*</sup></label><br>
            <textarea name="code" rows="30" cols="70"><?php echo $code;?></textarea>
            <span style="color: red"><?php echo $code_err;?></span>
            <br><br>
            <input type="submit" value="Submit">
            <input type="reset" value="Reset">
        </form><br>
        <a href="welcome.php">Go back</a><br><br>
        <p><sup style="color: red">*</sup> required fields</p>
    </div>

</body>
</html>