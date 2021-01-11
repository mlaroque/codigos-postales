<?php
global $is_cronjob;
if($is_cronjob){
    $path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
    require $path.'wp-load.php';
}
global $post;


function CP_custom_get_permalink($title, $needle = null){
    global $posts_cp;
    if($needle == 'estado'){
        $args = array(
            'post_type' => 'codigos-postales',
            'posts_per_page' => 1,
            'post_parent' => 0,
            'meta_key' => 'nombre_para_buscador',
            'meta_value' => $title
        );

        $estados = get_posts($args);
        $estado_id = $estados[0]->ID;

        return get_permalink($estado_id);

    }else{

        $args = array(
            'post_type' => 'codigos-postales',
            'posts_per_page' => -1, // debe de ser -1 PERO LO QUITO PORQUE ES MUY PESADO 
            'post_status' => 'publish' 
        );
        $posts_cp = get_posts($args);

        foreach($posts_cp as $post_cp){
            if($needle == 'municipio'){
                $tags = get_the_tags($post_cp->ID);
                $municipio_id = '';
    
                foreach($tags as $tag){
                    if(strpos($tag->name, 'municipio-'.$title) !== false){
                        $padre = get_post($post_cp->post_parent);
                        $permalink  = get_permalink($padre->ID);
                        return $permalink;
                    }
                }
            }else{
                if( strpos($post_cp->post_title, $title ) !== false && $post_cp->post_parent > 0 && $post_cp->post_status == 'publish'){
                    $permalink  = get_permalink($post_cp->ID);
                    return $permalink;
                }
            }
        }

        return '';
    }
        
}

function sanitize($title){
    $title = str_replace(' ','-', $title);
    $title = str_replace('á','a', $title);
    $title = str_replace('é','e', $title);
    $title = str_replace('í','i', $title);
    $title = str_replace('ó','o', $title);
    $title = str_replace('ú','u', $title);
    $title = str_replace('Á','a', $title);
    $title = str_replace('É','e', $title);
    $title = str_replace('Í','i', $title);
    $title = str_replace('Ó','o', $title);
    $title = str_replace('Ú','u', $title);
    $title = str_replace('[','', $title);
    $title = str_replace(']','', $title);
    $title = str_replace('(','', $title);
    $title = str_replace(')','', $title);
    $title = str_replace('ñ','n', $title);
    $title = str_replace('.','', $title);
    $title = strtolower($title);
    return $title;
}

function CP_conver_cp_str_to_links($codigos_str){
    return  '<a>'.str_replace(',', "</a>, <a>", $codigos_str . '</a>');

    /* 
    * Del string rango de codigos postales obtengo cada CP 
    * y lo convierto en un enlace y añado una coma como separador 
    */
    /* $codigos = explode(',', $codigos_str); // array de cp
    $total_codigos = count($codigos);

    $count = 0;
    
    ob_start();

    foreach($codigos as $cp):
        $count += 1;

        $cp_post = post_exists($cp, '','', 'codigos-postales');

        if($cp_post):  */
?>
            <!-- <a href="<?php //echo get_permalink($cp_post);?>"> <?php //echo $cp;?></a>  -->
        
        <?php //else: ?>
            
            <!-- <a> <?php //echo $cp;?></a>  -->
        
        <?php //endif; ?>
        
        <?php //if($count < $total_codigos): //para poner una coma despues de cada CP excepto el ultimo ?> 
            <!-- <span>, </span> -->
        <?php //endif; ?>
<?php
    /* endforeach;
    $listado_codigos = ob_get_clean();
    return $listado_codigos; */

}

?>