<?php  
	require_once 'config.php';
	session_start();
	if(isset($_SESSION['code_name'])){
		$name = $_SESSION['code_name'];
	$sql = "DELETE FROM code WHERE name = ?";
	if($stmt =mysqli_prepare($link,$sql)){
		mysqli_stmt_bind_param($stmt,"s",$param_name);
		$param_name = $name;
		mysqli_stmt_execute($stmt);
		
	}
}
mysqli_close($link);
header("location:welcome.php");
?>