<?php
$GLOBALS['is_cronjob'] = true;
include dirname(__FILE__, 2).'/functions/utils.php';

$host = 'localhost';
$db = 'codigosp_db';
$user = 'codigosp_user';
$pwd = 'gasherbrum-8000!';

try {
    $GLOBALS['conn'] = new PDO('mysql:host='.$host.';dbname='.$db.';charset=utf8', $user, $pwd);
}
catch (PDOException $e) {
    echo '<p>ERROR: No se conecto a la base de datos..!</p>';
    exit;
}
global $conn;

$sql = "SELECT distinct MUNICIPIO, ESTADO from LCMN_CODIGOS_POSTALES order by ESTADO;";

$limit = 10;
$file_created = 0;

foreach($conn->query($sql) as $row){
    if($file_created < $limit){

        $content_path = dirname(__FILE__,2).'/data/content/';
        $estado_sanitized = sanitize($row['ESTADO']);
        $municipio_sanitized = sanitize($row['MUNICIPIO']);

        $estado_dir_name = $content_path . $estado_sanitized;

        if( !file_exists($estado_dir_name.'/'.$municipio_sanitized .'.json') ){
            $GLOBALS['init_municipio'] = $row['MUNICIPIO'];
            $GLOBALS['init_estado'] = $row['ESTADO'];
            include dirname(__FILE__,2). '/functions/generar_cache_para_municipio.php';
            $file_created += 1;
        }else{
            /* $headers = "From: erick@lacomuna.mx\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            error_log('Ya existe: ' . $estado_dir_name.'/'.$municipio_sanitized .'.json', 1, 'erick@lacomuna.mx', $headers); */
            // error_log('Ya existe: ' . $estado_dir_name.'/'.$municipio_sanitized .'.json', 0);
        }

    }else{
        break;
    }
}
?>