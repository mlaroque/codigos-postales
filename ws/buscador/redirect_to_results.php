<?php
	$path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
	require $path.'wp-load.php';
	$response = array();

	$search_id = $_POST["lcmn_s"];

	//Verificar si es un post id
	$redirect_post_url = get_permalink($search_id);

	$response["url"] = $redirect_post_url;

	//Devolvemos la URL resultante
	$response["result"] = "success";
	echo json_encode($response);
?>