<?php $path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);

require $path.'wp-load.php'; 
include("header.php"); 

//$post = get_post($_SESSION['experto_id']);
//include "templates/questions.php"; 

wp_register_script( 'frontend.js', '/wp-content/plugins/codigos-postales/js/frontend.js', 
array('jquery'), '', true );
wp_enqueue_script( 'frontend.js' );

$css_file = dirname(__FILE__, 3).'/css/style.css'; 

?>

<style> <?php include_once $css_file; ?> </style> 

<div class="wrap">
    <h1 class="wp-heading-inline">Códigos Postales</h1>
    <hr class="wp-header-end">
    <div id="advanced-sortables" class="meta-box-sortables ui-sortable">
        <div class="postbox">
            <div class="postbox-header">
                <h2 class="hndle ui-sortable-handle">Cotizador del proyecto</h2>
            </div>

            <div class="inside">
                <label class="metas-label lc_label" for="shortcode_1">URL del cotizador</label>
                <!-- <input class="metas-input-txt lc_input" type="text" name="shortcode_1" id="shortcode_1" placeholder='Ej: [cotizador completo="si"]'> -->
                <input class="metas-input-txt lc_input" type="text" name="shortcode_1" id="shortcode_1" placeholder='Ej: https://guiapaqueteria.com/'>
                <button class="button button-primary button-large" id='shortcode_saver'>Guardar shortcode</button>
                <p id='msg_shortcode_exito' style='display:none;'>Credenciales guardadas exitosamente!</p>
                <p id='msg_shortcode_error' style='display:none; color:red;'>Se necesitan todos los campos</p>
            </div>
        </div>
    </div>

    
<hr>

    <div id="advanced-sortables" class="meta-box-sortables ui-sortable">
        <div class="postbox">
            <div class="postbox-header">
                <h2 class="hndle ui-sortable-handle">Generar los posts de códigos postales automáticamente</h2>
            </div>

            <div class="inside lc_generar">
                <p>
                	<a class="button button-primary button-large" href="admin.php?page=cp_dashboard&generar=ESTADO">Generar posts de Estados</a>
                	<a class="button button-primary button-large" href="admin.php?page=cp_dashboard&generar=MUNICIPIO">Generar posts de Municipios</a>
                	<a class="button button-primary button-large" href="admin.php?page=cp_dashboard&generar=COLONIA">Generar posts por Colonias</a>
                	<!-- <a class="button button-primary button-large" href="admin.php?page=cp_dashboard&generar=CP">Generar posts de Códigos Postales</a> -->
                	<a class="button button-primary button-large" href="admin.php?page=cp_dashboard&generar=TITLES">Actualizar Titles -> SEO</a>
                	<a class="button button-primary button-large" href="admin.php?page=cp_dashboard&generar=BUSCADOR">Generar archivos para el buscador</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?php
if($_GET['generar']){
    include dirname(__DIR__, 2).'/functions/generar_posts.php';
}
?>
<?php include "footer.php"; ?>