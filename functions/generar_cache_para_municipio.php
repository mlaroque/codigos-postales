<?php

// include __DIR__. '/utils.php';

/* $host = 'localhost';
$db = 'codigosp_db';
$user = 'codigosp_user';
$pwd = 'gasherbrum-8000!';

try {
    $GLOBALS['conn'] = new PDO('mysql:host='.$host.';dbname='.$db.';charset=utf8', $user, $pwd);
}
catch (PDOException $e) {
    echo '<p>ERROR: No se conecto a la base de datos..!</p>';
    exit;
} */

global $conn;
global $init_municipio;
global $init_estado;

$colonias_array = array();
// $init_municipio = 'Hermosillo';
// $init_estado = 'Sonora';

$estado_dir_name = sanitize($init_estado);
$municipio_file_name = sanitize($init_municipio);

$sql = "SELECT * FROM LCMN_CODIGOS_POSTALES WHERE MUNICIPIO = \"$init_municipio\" AND ESTADO = \"$init_estado\" ORDER BY COLONIA";
foreach($conn->query($sql) as $row){
    
    $col_slug = sanitize($row['COLONIA']);
    $estado_slug = sanitize($row['ESTADO']);

    $obj['colonia'] = $row['COLONIA'];
    $obj['col-slug'] =  $col_slug;
    $obj['asentamiento'] = $row['TIPO_ASENTAMIENTO'];
    $obj['cp'] = $row['CP'];
    $obj['estado'] = $row['ESTADO'];
    $obj['estado-slug'] = $estado_slug;
    $obj['url'] = 'https://cp.guiapaqueteria.com/codigos-postales/'.$estado_slug .'/'.$col_slug.'/';
    array_push($colonias_array, $obj);
}

$json = json_encode($colonias_array,  JSON_UNESCAPED_UNICODE);

$file_path = dirname(__FILE__, 2).'/data/content/'.$estado_dir_name.'/'.$municipio_file_name.'.json' ;
$val = file_put_contents( $file_path, $json );

$headers = "From: erick@lacomuna.mx\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

if($val !== FALSE){
    // error_log( 'Archivo creado: '. $file_path ,1, 'erick@lacomuna.mx', $headers);
    error_log( 'Archivo creado: '. $file_path ,0);
}else{
    // error_log( 'Falló al crear el archivo: '. $file_path, 1, 'erick@lacomuna.mx', $headers);
    error_log( 'Falló al crear el archivo: '. $file_path, 0);
}


?>