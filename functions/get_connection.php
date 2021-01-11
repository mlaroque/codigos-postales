<?php
global $wpdb;

$host = $wpdb->dbhost;
$db = $wpdb->dbname;
$user = $wpdb->dbuser;
$pwd = $wpdb->dbpassword;

try {
    $GLOBALS['conn'] = new PDO('mysql:host='.$host.';dbname='.$db.';charset=utf8', $user, $pwd);
}
catch (PDOException $e) {
    echo '<p>ERROR: No se conecto a la base de datos..!</p>';
    exit;
}

?>