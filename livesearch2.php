<?php
	require_once 'config.php';
	$sql = "SELECT id FROM code where name = ?";
	$name = $_GET['q'];
	$response="";
	if($stmt = mysqli_prepare($link,$sql)){
		mysqli_stmt_bind_param($stmt,"s",$param_name);
		$param_name=$name;
		if(mysqli_stmt_execute($stmt)){
			mysqli_stmt_store_result($stmt);
			if(mysqli_stmt_num_rows($stmt)==1){
				$response="Name unavailable";
			}else{
				$response="Name available";
			}
		}
	}
	echo (string)$response;
  ?>