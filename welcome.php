<?php
require 'config.php';
session_start();
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
 
 $_SESSION['username'] = "Guest";
}
$code= $name = $created_by = $language ="";
$name_err = "";
$flag = false;

	
if ($_SERVER["REQUEST_METHOD"]=="GET") {
	if(isset($_GET["code_name"])){


	if(empty(trim($_GET["code_name"]))){
		$name_err = "Please select an option";
	}else{
		$name = trim($_GET['code_name']);
		$_SESSION['code_name'] = $name;
	}
	if(empty($name_err)){
		$sql = "SELECT snippet, created_by, access, anonymous, language FROM code WHERE name = ?";
		if($stmt = mysqli_prepare($link,$sql)){
			mysqli_stmt_bind_param($stmt,"s",$param_name);
			$param_name=$name;
			if(mysqli_stmt_execute($stmt)){
				mysqli_stmt_store_result($stmt);
				mysqli_stmt_bind_result($stmt,$param_code,$param_creator,$param_access,$param_anonymous,$param_language);
				mysqli_stmt_fetch($stmt);
				if($param_access=="Public" || ($param_access=="Private" && $param_creator==$_SESSION['username'])){
				$code = $param_code;
				$language=$param_language;
			    }else{
			    	$code = "You do not have access to this code";
			    	$flag = true;
			    }
				if($param_anonymous==1){
					$created_by = "anonymous";
				}else{
					$created_by = $param_creator;
				}
				

			}else{
				echo "Oops! Something went wrong, Please try again later";
			}
		}

		mysqli_stmt_close($stmt);

	}


	
}
	
}

$run=true;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Welcome</title>
	<link rel="stylesheet" type="text/css" href="prism.css">
	<script type="text/javascript" src="prism.js"></script>
</head>

<body>
	<h3>Welcome <?php echo $_SESSION['username'];  ?></h3>
	<a href="add.php" style="margin-right: 30px">Add code</a>
	<?php
      $url="";
      $url_hint="";
      if($_SESSION['username']== "Guest"){
      	$url='login.php';
      	$url_hint="Login";
      }
      else{
      	$url = 'logout.php';
      	$url_hint="Logout";
      }
      echo "<a href=".$url.">".$url_hint."</a>";
	  ?>
	  <br><br>
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
		<label>Choose the code </label>
		<select name="code_name">
			<option disabled selected value style="display: none">-- select an option --</option>
			<?php 
				$sql = "SELECT name, created_by, access FROM code";
				if($result = mysqli_query($link,$sql)){
					while ($row = mysqli_fetch_array($result)) {
						if($row['access']=="Public" || $row['created_by']== $_SESSION['username'] ){
						echo "<option value=".$row['name'].">".$row['name']."</option>";
					}
				}
				}
				else{
					echo "Oops! Something went wrong, Please try again later";
				}

			 ?>
			
		</select>
		<span style="color: red"><?php echo $name_err;  ?></span>
		<br><br>
		<input type="submit" value="Submit">
	</form>
	<br>
	
	   <pre class=\"language-<?php echo $language;?>"><?php echo trim(highlight_string($code),'1');?></pre><br>
	   <?php
		if(!empty($created_by) && $flag==false){
			echo "<p>Posted by ".$created_by."</p><br>";
			} 
	
	if($created_by==$_SESSION['username']){  	
	echo  "<button onclick=\"del()\" style=\"margin-right : 30px\">Delete post</button>";
	echo "<button onclick=\"edit()\">Edit post</button>";
	}
	?>
<script type="text/javascript">
	function del(){
		document.location.href="delete.php";
	}
	function edit() {
		document.location.href="save.php";
	}
</script>

</body>

</html>