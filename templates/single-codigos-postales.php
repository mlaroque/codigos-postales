<?php
/*
Template Name: Códigos postales
Template Post Type: codigos-postales, page, post
*/
get_header();

include dirname(__DIR__, 1). '/functions/utils.php';
require_once ABSPATH . '/wp-admin/includes/post.php'; 

global $post;
$args = array(
    'post_type' => 'codigos-postales',
    'posts_per_page' => -1,
    'post_parent' => 0,
    'orderby' => 'title',
    'order' => 'ASC',
    'post__not_int' => array($post->ID) //DESCOMENTAR, ESTE SI VA
);
$estados_cp = get_posts($args);

$ancestors = count(get_post_ancestors($post->ID));

if($ancestors == 2){
    $post_level = "colonia";
    
    /* **** header/título **** */
    $ancestors_title = array();
    foreach(get_post_ancestors($post->ID) as $post_ancestor){
        $post_ancestor = get_post($post_ancestor);
        array_push($ancestors_title, $post_ancestor->post_title);
    }
    $posts_ancestors_title = implode(', ', $ancestors_title);
    $title = "Códigos Postales de $post->post_title en $posts_ancestors_title";
    ///* titulo */
    
}else if($ancestors == 1){
    $post_level = 'municipio';
    $post_estado = get_post($post->post_parent);
    $title = "Códigos Postales del Municipio $post->post_title en $post_estado->post_title";
}else if($ancestors == 0){
    $post_level = 'estado';
    $title = "Códigos Postales del Estado de $post->post_title";
}

$ancestors_array = get_post_ancestors($post->ID);
sort($ancestors_array);

$page_listado = get_page_by_title('Códigos postales');
?> 

<article class="topCont" role="article">
    <div class="container">

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="https://guiapaqueteria.com">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo get_permalink($page_listado->ID); ?>"> <?php echo $page_listado->post_title; ?></a>  </li>
                            
                    <?php 
                        foreach( $ancestors_array as $ancestor):
                                    
                        $post_ancestor = get_post($ancestor);
                    ?>
                                
                    <li class="breadcrumb-item"> <a href="<?php echo get_permalink($ancestor); ?>"> <?php echo get_post_meta($ancestor, 'nombre_para_buscador', true);?> </a> </li>
                            
                    <?php endforeach;?>

                    <li class="breadcrumb-item active"> <?php echo get_post_meta($post->ID, 'nombre_para_buscador', true); ?> </li>
                </ol>
            </div>
        </div>
        
        <div class="row">
            <!--GRANDE -->
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                <h1> <?php echo $post->post_title; ?> </h1>
                <p> <?php echo $post->post_content; ?> </p>

                <?php //include(__DIR__.'/template-parts/buscador.php');?>

                <?php include(__DIR__.'/template-parts/template-'.$post_level.'.php'); ?>


            </div>
        </div>


        <div class="row">
            <div class="col--xs-12 col-sm-12 col-md-12 col-lg-12 CPlistR">
                <h2 class="text-center">Encuentra Códigos Postales por Estado</h2>
                <ul>
                    <?php foreach($estados_cp as $estado):?>
                    <li><a href="<?php echo get_permalink($estado->ID)?>"> <?php echo $estado->post_title; ?></a></li>
                    <?php endforeach; ?>

                </ul>
            </div>
        </div>
    </div>

</article>

<?php  
$json_file = dirname(__FILE__, 2). '/data/shortcode_name.json';

$json = json_decode( file_get_contents($json_file), true );
$URL_cotizador = $json['shortcode_1'];

?>

<?php if($URL_cotizador):?>

<div class="container-fluid shadow CPcta">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 text-center">
                <img src="<?php echo get_template_directory_uri() . '/images/CPcta.svg'; ?>" />     
            </div>

            <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 text-center">
                <h2>¿Deseas hacer algún envío a este código postal?</h2>

                <a class="btn btn-CPcta tdn shadow" href="<?php echo $URL_cotizador; ?>">Cotiza Ahora</a>
            </div>
        </div>
    </div>
</div>
<?php endif;?>

<?php
get_footer();

?>