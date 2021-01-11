<?php
include dirname(__DIR__, 2).'/functions/get_connection.php'; 
global $conn;
global $post;

$post_padre = get_post($post->post_parent);

$tags = get_the_tags($post->ID);
$municipio_title = '';
$estado = '';

foreach($tags as $tag){
    if(strpos($tag->name, 'municipio-') !== false){
        $municipio_title = str_replace('municipio-', '', $tag->name);
    }
    if(strpos($tag->name, 'estado-') !== false){
        $estado = str_replace('estado-', '', $tag->name);
    }
}
$query_municipio = $municipio_title;
// $sql = "SELECT * FROM LCMN_CODIGOS_POSTALES WHERE MUNICIPIO = \"$municipio_title\" AND ESTADO = \"$estado\" ORDER BY COLONIA LIMIT 20";
$sql = "SELECT * FROM LCMN_CODIGOS_POSTALES WHERE MUNICIPIO = \"$municipio_title\" AND ESTADO = \"$estado\" ORDER BY COLONIA";
// echo $sql;
?>
<div class="table-responsive CPtableBox shadow">
    <table class="CPtable">
        <thead>
            <tr>
                <th>Asentamiento</th>
                <th>Tipo de Asentamiento</th>
                <th>Código Postal</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>

        <?php
        $post_padre = get_post($post->post_parent);
        $padre_slug = $post_padre->post_name;
        $file_path = dirname(__FILE__, 3) . '/data/content/'.$padre_slug.'/'.$post->post_name.'.json';
        ?>

        <?php if(file_exists($file_path)):?>

        <?php 
            $json = file_get_contents($file_path); 
            $json = json_decode($json, true);

            foreach($json as $obj_colonia):
        ?>  
                <tr>
                    <td class="CPestado"><a href="<?php echo $obj_colonia['url'];?>"> <?php echo $obj_colonia['colonia'];?> </a></td>
                    <td class="colonia"><a> <?php echo $obj_colonia['asentamiento'];?> </a></td>
                    <td class="CPcp"> <?php echo '<a>'.$obj_colonia['cp'].'</a>';?></td>
                    <td class="CPcp"><a> <?php echo $obj_colonia['estado'];?> </a></td>
                </tr>
            <?php endforeach;?>

        <?php else:?>
        <?php foreach($conn->query($sql) as $row):?>
            <?php
        
            $args_colonia = array(
                'post_type' => 'codigos-postales',
                'meta_key' => 'nombre_para_buscador',
                'meta_value' =>  $row['COLONIA'],
                'post_parent' => $post->ID,
                'posts_per_page' => 1,
                'post_status' => 'publish'
            );
            $post_colonia = get_posts($args_colonia);
            $post_colonia = $post_colonia[0];

            ?>

            <tr>
                <?php if($post_colonia):?>
                    <td class="CPestado"><a href="<?php echo get_permalink($post_colonia->ID);?>"> <?php echo $row['COLONIA'];?> </a></td>
                <?php else :?>
                    <td class="CPestado"><a> <?php echo $row['COLONIA'];?> </a></td>
                <?php endif; ?>
                <td class="colonia"><a> <?php echo $row['TIPO_ASENTAMIENTO'];?> </a></td>
                <!-- <td class="CPcp"> <?php //echo CP_conver_cp_str_to_links($row['CP']);?></td> -->
                <td class="CPcp"> <?php echo '<a>'.$row['CP'].'</a>';?></td>
                <td class="CPcp"><a> <?php echo $row['ESTADO'];?> </a></td>
            </tr>

        <?php endforeach;?>
        <?php endif;?>
        </tbody>
    </table>
</div>

<?php include __DIR__. '/mapa.php';  ?>