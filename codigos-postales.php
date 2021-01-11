<?php
/**
 * Plugin Name:       Códigos postales
 * Description:       Plugin para crear post type y artículos de códigos postales.
 * Version:           1.0.3
 * Author:            Erick Comunero
 * Author URI:        https://lacomuna.mx/
 * License:           GPL
 */

defined('ABSPATH') or die("Bye bye");

require(__DIR__.'/functions/plugin_init.php');

global $post;

/* Enlazamos el template del single con el post type */
add_filter('single_template', 'CP_single_template');
function CP_single_template($single) {

    global $post;

    /* Checks for single template by post type */
    if ( $post->post_type == 'codigos-postales' ) {
        if ( file_exists( __DIR__ . '/templates/single-codigos-postales.php' ) ) {
            return  __DIR__ . '/templates/single-codigos-postales.php';
        }
    }

    return $single;

}

//Native ad para empresas de paqueteria
add_shortcode( 'CP_listado_estados', 'CP_listado_estados');

function CP_listado_estados( $content){

    ob_start();
        include (__DIR__ ."/templates/template-parts/listado-estados.php");
    $content .= ob_get_clean();
        
    return $content;
}

 
/**
 * Activate the plugin.
 */
function CP_plugin_init() { 
    // Trigger our function that registers the custom post type plugin.
    CP_create_post_type(); 

    //Trigger crear página de listado de cp
    CP_create_page_listado_cp();

    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules(); 

    
}
register_activation_hook( __FILE__, 'CP_plugin_init' );


 // Hook for adding admin menus
 add_action('admin_menu', 'lcmn_CP_add_pages');


/* Adds a meta box to the post edit screen */
add_action( 'add_meta_boxes', 'nombre_para_buscador_custom_box' );
function nombre_para_buscador_custom_box() {
    $screens = array( 'codigos-postales' );
    foreach ( $screens as $screen ) {
        add_meta_box(
            'nombre_buscador_data_id',            // Unique ID
            'Nombre del artículo para el buscador',      // Box title
            'nombre_buscador_inner_custom_box',  // Content callback
             $screen                      // post type
        );
    }
}

/* Prints the box content */
function nombre_buscador_inner_custom_box( $post ) {
?>
    <input name="nombre_para_buscador" value="<?php echo get_post_meta( $post->ID, 'nombre_para_buscador', true ); ?>" placeholder="Ej: Tulum" style="width:100%;"/><br />
<?php
}
?>