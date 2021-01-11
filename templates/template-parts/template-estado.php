<?php
include dirname(__DIR__, 2).'/functions/get_connection.php'; 

global $conn;
global $post;

$estado = $post->post_title;
$tags = get_the_tags($post->ID);
$estado = '';

foreach($tags as $tag){
    if(strpos($tag->name, 'estado-') !== false){
        $estado = str_replace('estado-', '', $tag->name);
    }
}

?>

<!-- <h2 class="text-center">Municipios de <?php //echo $estado; ?></h2> -->

<div class="table-responsive CPtableBox shadow">
	<table class="CPtable">
		<thead>
			<tr>
				<th>Municipio</th>

				<th>Rango de Códigos Postales</th>
			</tr>
		</thead>

		<tbody>

            <?php 
                $pre_sql = "SET SESSION group_concat_max_len = 1000000;";
                $conn->query($pre_sql);

                $sql = "SELECT municipio, GROUP_CONCAT(DISTINCT cp ORDER BY cp ASC separator '<a/>, <a>') as rango from LCMN_CODIGOS_POSTALES where estado = \"$estado\" GROUP BY municipio ";
                // echo $sql;

                foreach($conn->query($sql) as $row):
                    $municipio = $row['municipio'];
                    
                    $args_municipio = array(
                        'post_type' => 'codigos-postales',
                        'meta_key' => 'nombre_para_buscador',
                        'meta_value' =>  $municipio,
                        'post_parent' => $post->ID,
                        'posts_per_page' => 1,
                        'post_status' => 'publish'
                    );
                    $post_municipio = get_posts($args_municipio);
                    
                    $post_municipio = $post_municipio[0];


            ?>

                <tr>
                    <td class="CPestado" data-th="Municipio"><a href="<?php echo get_permalink($post_municipio->ID); ?>"> <?php echo $row['municipio']; ?> </a></td>
                    <!-- <td class="CPcp" data-th="Rango de Códigos Postales"> <?php //echo CP_conver_cp_str_to_links($row['rango']);?></td> -->
                    <td class="CPcp" data-th="Rango de Códigos Postales"> <?php echo '<a>'.$row['rango'].'</a>'; ?></td>
                </tr>

            <?php endforeach;?>

		</tbody>

	</table>
</div>



