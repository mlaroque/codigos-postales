<?php
ini_set('max_execution_time', 300);
$path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
require $path.'wp-load.php';

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

$count = 1;
// count_db = lo que esta en base de datos en la nueva tabla con la columna unica de inserciones.
// escala = lo que esta en base de datos
$sql = "SELECT INSERCIONES, ESCALA FROM LCMN_INSERCIONES WHERE TIPO='TITLES';";
$inserciones = '';
$escala = '';
foreach($conn->query($sql) as $row){
    $inserciones = $row['INSERCIONES'];
    $escala = $row['ESCALA'];
}

$min_limit = $inserciones + 1;
$max_limit = $inserciones + $escala;
// $sql_posts = "SELECT ID FROM codigosp_db.wp_posts where post_type = 'codigos-postales' limit $min_limit, $max_limit;";
$sql_posts = "SELECT ID FROM codigosp_db.wp_posts where post_type = 'codigos-postales' limit $inserciones, $escala;";

$posts_ids = array();
foreach($conn->query($sql_posts) as $row){
  if( !in_array($row['ID'], $posts_ids)){
    array_push($posts_ids, $row['ID']);
  }  
}

$args = array(
    'post_type' => 'codigos-postales',
    'post_status' => 'publish',
    'post__in' => $posts_ids,
    'posts_per_page' => -1
);
$articulos = get_posts($args);

$total_art = count($articulos);

foreach($articulos as $art_cp){

  /*   if($count > ($inserciones + $escala)){
        break;
    } */

    $ancestors = get_post_ancestors($art_cp->ID);
    $total_ancestors = count( $ancestors );
    if($total_ancestors == 0){
      $prev_title = get_post_meta($art_cp->ID, 'nombre_para_buscador', true);
      $title_seo = "Códigos Postales del Estado de $prev_title ";
    }else if($total_ancestors == 1){
      $prev_parent_title = get_post_meta($art_cp->post_parent, 'nombre_para_buscador', true);
      $title = get_post_meta($art_cp->ID, 'nombre_para_buscador', true);
      $title_seo = "Códigos Postales del Municipio $title en $prev_parent_title";
    }else if($total_ancestors == 2){
      sort($ancestors);
      $estado_id = $ancestors[0];
      $municipio_id = $ancestors[1];

      $title_name = get_post_meta($art_cp->ID, 'nombre_para_buscador', true);
      $prev_estado_title = get_post_meta($estado_id, 'nombre_para_buscador', true);
      $prev_municipio_title = get_post_meta($municipio_id, 'nombre_para_buscador', true);

      $title_seo = "Códigos Postales de $title_name en $prev_municipio_title, $prev_estado_title ";
    }

    $update_args = array(
      'ID' => $art_cp->ID,
      'post_title' => $title_seo
    );

    wp_update_post($update_args, true );
    // $count += 1;

}

$total = $inserciones + $escala;

  $sql = "UPDATE LCMN_INSERCIONES
          SET INSERCIONES = $total
          WHERE TIPO = 'TITLES'";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
//   echo "Se crearon todos los posts";

?>