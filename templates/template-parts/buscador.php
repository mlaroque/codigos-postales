<?php

$keyword_se = $_GET['keyword_id_se'];

if($keyword_se):?>
    <script type="text/javascript">
        window.location = "<?php echo get_permalink($keyword_se); ?>";
    </script>
<?php endif;

global $post;
$path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);

require $path.'wp-load.php'; 
include("header.php"); 

wp_register_script( 'buscador.js', '/wp-content/plugins/codigos-postales/js/buscador.js', null, '', true );
wp_enqueue_script( 'buscador.js' );

?> 
<!--BUSCADOR-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@8.1.0/dist/css/autoComplete.min.css">
<script src="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@8.1.0/dist/js/autoComplete.min.js"></script>
    <form id="buscador_form">
        <div class="body" align="center">
            <div class="autoComplete_wrapper">
                <input id="autoComplete" type="text" tabindex="1">
            </div>
            <input name="lcmn_s" id="lcmn_s" type="hidden" class="selection">
            <button type="submit">Buscar</button>
        </div>
    </form>