<?php

  $json = "[";

  $path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
  require $path.'wp-load.php';

  //creamos conexion a BD
  include __DIR__ . '/get_connection.php';
  global $conn;

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

    //TODO: obtener los registros
    foreach($conn->query($sql) as $row){

      $sub_sql = "SELECT * FROM wp_postmeta where post_id = " . $row['ID'] . " and meta_key= 'nombre_para_buscador'";
      $title = "";

      foreach($conn->query($sub_sql) as $sub_row){
        $title = str_replace('"','',$sub_row['meta_value']);
      }

      if($nivel == 'estado'){
        $json .= '{"e":"'.$title.'","m":"","c":"","id":"'.$row["ID"].'"},';
      }else if($nivel == 'municipio'){
        $json .= '{"e":"","m":"'.$title.'","c":"","id":"'.$row["ID"].'"},';
      }else if($nivel == 'colonia'){
        $json .= '{"e":"","m":"","c":"'.$title.'","id":"'.$row["ID"].'"},';
      }

    }

  }
  $json = rtrim($json,",");
  $json .= "]";
  
  file_put_contents(dirname(__FILE__,2).'/data/buscador.json', $json);


?>