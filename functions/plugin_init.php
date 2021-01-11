<?php

 /**
 * Register the "codigos-postales" custom post type
 */

add_action( 'init', 'CP_create_post_type' );

function CP_create_post_type() {
    if( !post_type_exists('codigos-postales') ){
        register_post_type( 'codigos-postales',  
            array(  
                'labels' => array(  
                    'name' => __( 'Códigos postales' ),  
                    'singular_name' => __( 'Código postal' )  
                ),  
            'public' => true,  
            'menu_position' => 13,
            'hierarchical' => true,     
            'taxonomies' => array( 'category', 'post_tag' ),
            'supports' => array( 'title', 'editor', 'author', 'comments', 'thumbnail', 'excerpt', 'page-attributes')
            )  
        );
    }
    
} 

add_shortcode( 'CP_buscador_codigos_postales', 'CP_buscador_codigos_postales');

function CP_buscador_codigos_postales( $content){

        ob_start();
        include dirname(__FILE__,2). "/templates/template-parts/buscador.php";
        $content .= ob_get_clean();
        
    return $content;
}

 
function CP_create_page_listado_cp(){
    if( !post_exists('Códigos postales', '', '', 'codigos-postales') ){
        $cp_page_args = array(
            'post_type' => 'codigos-postales',
            'post_title' => 'Códigos postales',
            'post_parent' => 0,
            'post_content' => '[CP_listado_estados]'
        );

        wp_insert_post($cp_page_args);

    }
}

 // action function for above hook
function lcmn_CP_add_pages() {
	
	//Add a new menu
	add_menu_page( 'Códigos Postales', 'Códigos Postales', 'manage_options', 'cp_dashboard', 'lcmn_CP_dashboard_page', '', 90 );

	// add_submenu_page( 'dashboard', 'Configuración', 'Configuración', 'manage_options', 'lcmn_aae_settings_id','lcmn_settings_aae_page');
	// add_submenu_page( 'dashboard', 'Preguntas', 'Preguntas', 'manage_options', 'lcmn_aae_questions_id','lcmn_questions_aae_page');
	// add_submenu_page( 'dashboard', 'Usuarios', 'Usuarios', 'manage_options', 'lcmn_aae_users_id','lcmn_users_aae_page');
}

function lcmn_CP_dashboard_page() {
	// echo  dirname(__FILE__, 2) . '/templates/plugin-frontend/index.php';
    ob_start();
    include dirname(__FILE__, 2) . '/templates/plugin-frontend/index.php';			
    echo ob_get_clean();
}

?>