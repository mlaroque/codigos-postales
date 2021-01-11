<?php
$target = $_GET['generar']; //ESTADO, MUNICIPIO, CP o COLONIA

// $target = "COLONIA";

$path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
require $path.'wp-load.php';

//creamos conexion a BD
include __DIR__ . '/get_connection.php';
global $conn;

//por cada row crear un POST en el post type codigos-postales (SI NO EXISTE) dependiendo del nivel 
if($target == 'ESTADO'){ //nivel 1 (post padre)

    //preparar una query para extraer todos los targets correspondientes
    $sql = "SELECT $target FROM LCMN_CODIGOS_POSTALES GROUP BY $target "; //agrupar estados

    foreach($conn->query($sql) as $row){
      $estado = $row['ESTADO'];

      if(!post_exists($estado, '', '', 'codigos-postales')){
        $args = array(
          'post_title' => $estado,
          'post_parent' => 0,
          'post_type' => 'codigos-postales',
          'post_status' => 'publish' // ELIMINAR, CAMBIAR A PUBLIC
        );

        $post_id = wp_insert_post($args);

        update_post_meta($post_id, 'nombre_para_buscador', $estado);

        wp_set_post_tags($post_id, array($estado, "estado-$estado"));

      }
      
    }

    echo "Se crearon todos los posts";
    
      
}else if($target == 'MUNICIPIO'){ //nivel 2 (post hijo

  $sql = "SELECT ESTADO, MUNICIPIO FROM LCMN_CODIGOS_POSTALES GROUP BY ESTADO, MUNICIPIO";

  foreach($conn->query($sql) as $row){

    $estado = $row['ESTADO'];
    $municipio = $row['MUNICIPIO'];
    
    //obtener ID del post (Post padre: Estado)
    $post_estado_ID = post_exists($estado, '', '', 'codigos-postales');

    if( $post_estado_ID ){
      $args = array(
        'post_title' => $municipio,
        'post_parent' => $post_estado_ID,
        'post_type' => 'codigos-postales',
        'post_status' => 'publish' 
      );

      $post_id = wp_insert_post($args);
      update_post_meta($post_id, 'nombre_para_buscador', $municipio);

      wp_set_post_tags($post_id, array($municipio, "municipio-$municipio", $estado, "estado-$estado"));
      
    }
  }

  echo "Se crearon todos los posts";


}else if($target == 'COLONIA'){ //nivel 3 (post nieto) 

    
  $args = array(
    'post_type' => 'codigos-postales',
    'posts_per_page' => -1,
    'post_status' => 'publish', 
    'orderby' => 'parent',
    'order' => 'ASC'
  );

  $posts_codigos_postales = get_posts($args);

  $count = 1;
  // count_db = lo que esta en base de datos en la nueva tabla con la columna unica de inserciones.
  // escala = lo que esta en base de datos
  $sql = "SELECT INSERCIONES, ESCALA FROM LCMN_INSERCIONES WHERE TIPO='COLONIA';";
  $inserciones = '';
  $escala = '';
  foreach($conn->query($sql) as $row){
    $inserciones = $row['INSERCIONES'];
    $escala = $row['ESCALA'];

  }

  foreach($posts_codigos_postales as $post_cp){

    if($post_cp->post_parent >0){ // si es un post hijo (Municipio)

      //filtrar posts hijos para que solo sean municipios (cuyo padre sea un post nivel 1)
      $post_padre = get_post($post_cp->post_parent);
      if ($post_padre->post_parent == 0){
      
        // si count > count_db + escala entonces break
        if($count > ($inserciones + $escala)){
        break;
        }

        $municipio_name = $post_cp->post_title;
        $municipio_id = $post_cp->ID;

        $sql = "SELECT COLONIA FROM LCMN_CODIGOS_POSTALES where MUNICIPIO = \"$municipio_name\" ";

        foreach($conn->query($sql) as $row){

          // solo ejecuta el codigo siguiente si el count es > count_db y si count <= count_db + escala
          if($count > $inserciones && $count <= ($escala + $inserciones)){
            $colonia = $row['COLONIA'];
            
            $args_colonia = array(
              'post_title' => $colonia,
              'post_parent' => $municipio_id,
              'post_type' => 'codigos-postales',
              'post_status' => 'publish'
            );

            $post_id = wp_insert_post($args_colonia);
            update_post_meta($post_id, 'nombre_para_buscador', $colonia);

            wp_set_post_tags($post_id, array($colonia, "colonia-$colonia", $municipio_name, "municipio-$municipio_name"));

          } // end if
          $count += 1;

        } // end foreach
      }
    }

  }  

  $total = $inserciones + $escala;

  $sql = "UPDATE LCMN_INSERCIONES
          SET INSERCIONES = $total
          WHERE ID = 1";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  echo "Se crearon todos los posts";


}else if($target == 'CP'){ //nivel 4 (nivel mas bajo)

}else if($target == 'BUSCADOR'){
  
  $niveles = array('estado', 'municipio', 'colonia');

  foreach($niveles as $nivel){
    $file_name = 'se_list_'.$nivel . 's.json'; //ej: list_estados.json, list_municipios.json
    
    if($nivel == 'estado'){
      $sql = "SELECT * from wp_posts where post_type ='codigos-postales' and post_parent = 0";
    }else if ($nivel == 'municipio'){
      $sql = "SELECT * from wp_posts where post_type ='codigos-postales' and post_parent in (SELECT id from wp_posts where post_type ='codigos-postales' and post_parent = 0)";
    }else if($nivel == 'colonia'){
      $sql = "SELECT * from wp_posts where post_type = 'codigos-postales' and post_parent in (select id from wp_posts where post_type ='codigos-postales' and post_parent in (SELECT id from wp_posts where post_type ='codigos-postales' and post_parent = 0))";
    }

    $posts_array = array();

    //TODO: obtener los registros
    foreach($conn->query($sql) as $row){

      $sub_sql = "SELECT * FROM wp_postmeta where post_id = " . $row['ID'] . " and meta_key= 'nombre_para_buscador'";
      $title = "";

      foreach($conn->query($sub_sql) as $sub_row){
        $title = $sub_row['meta_value'];
      }

      $item = array(
        'name' => $title,
        'id' => $row['ID'],
        'guid' => $row['guid']
      );

      array_push($posts_array, $item);
    }

    //TODO: armar un json

    $json = json_encode($posts_array, JSON_UNESCAPED_UNICODE);

    //TODO: guardar cada json en un archivo

    file_put_contents(dirname(__FILE__,2).'/data'.'/'.$file_name, $json);

  }
}else if($target == 'TITLES'){
  $args = array(
    'post_type' => 'codigos-postales',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'ID',
    'order' => 'ASC'
  );
  echo 'antes de la carga';
  $articulos = get_posts($args);
  echo 'output: '.count($articulos);

  foreach($articulos as $art_cp){
    $ancestors = get_post_ancestors($art_cp->ID);
    $total_ancestors = count( $ancestors );
    if($total_ancestors == 0){
      $prev_title = get_post_meta($art_cp->ID, 'nombre_para_buscador', true);
      $title_seo = "Códigos Postales del Estado de $prev_title ";
    }else if($total_ancestors == 1){
      $prev_title = get_post_meta($art_cp->post_parent, 'nombre_para_buscador', true);
      $title_seo = "Códigos Postales del Municipio $art_cp->post_title en $prev_title";
    }else if($total_ancestors == 2){
      sort($ancestors);
      $estado_id = $ancestors[0];
      $municipio_id = $ancestors[1];

      $prev_estado_title = get_post_meta($estado_id, 'nombre_para_buscador', true);
      $prev_municipio_title = get_post_meta($municipio_id, 'nombre_para_buscador', true);

      $title_seo = "Códigos Postales de $art_cp->post_title en $prev_municipio_title, $prev_estado_title ";
    }

    $update_args = array(
      'ID' => $art_cp->ID,
      'post_title' => $title_seo
    );

    wp_update_post($update_args, true );

  }
}




?>