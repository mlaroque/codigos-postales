<?php
include dirname(__DIR__, 2).'/functions/get_connection.php'; 
global $conn;
global $post;

$post_municipio = get_post($post->post_parent);
// $municipio_title = $post_municipio->post_title;

$tags = get_the_tags($post->ID);
$colonia = '';
$municipio_title = '';
foreach($tags as $tag){
    if(strpos($tag->name, 'colonia') !== false){
        $colonia = str_replace('colonia-', '', $tag->name);
    }
    if(strpos($tag->name, 'municipio-') !== false){
        $municipio_title = str_replace('municipio-', '', $tag->name);
    }
}

$sql = "SELECT * FROM LCMN_CODIGOS_POSTALES 
        WHERE COLONIA = \"$colonia\" 
        AND MUNICIPIO = \"$municipio_title\"";
// echo $sql;

?>

<div class="table-responsive CPtableBox shadow">
    <table class="CPtable">
        <thead>
            <tr>
                <th>Asentamiento</th>
                <th>Tipo de Asentamiento</th>
                <th>Código Postal</th>
                <th>Municipio</th>
                <th>Estado</th>
                <!-- <th>Municipio / Alcaldía</th> -->
                <!-- <th>Ciudad</th> -->
            </tr>
        </thead>
        <tbody>
        <?php foreach($conn->query($sql) as $row):?>

            <tr>
                <td class="CPestado"><a> <?php echo $row['COLONIA'];?> </a></td>
                <td class="colonia"><a> <?php echo $row['TIPO_ASENTAMIENTO'];?> </a></td>
                <td class="CPcp"> <?php echo $row['CP'];?></td>
                <td class="CPcp"><a href="<?php echo get_permalink($post_municipio->ID);?>"> <?php echo $row['MUNICIPIO'];?> </a></td>
                <td class="CPcp"><a href="<?php echo get_permalink($post_municipio->post_parent);?>"> <?php echo $row['ESTADO'];?> </a></td>
                <!-- <td class="CPcp"><a href="<?php //echo CP_custom_get_permalink($row['MUNICIPIO']);?>"> <?php //echo $row['MUNICIPIO'];?> </a></td> -->
                <!-- <td class="CPcp">Playa del Carmen</td> -->
            </tr>

        <?php endforeach;?>
        </tbody>
    </table>
</div>

<!-- <div class="row CPlistR">
    <h2 class="text-center">Listado CP</h2>
    <ul class="fiveColist text-center">

        <?php foreach($conn->query($sql) as $row):?>

        <li>
            <a> <?php echo $row['CP'];?> </a>
        </li>

        <?php endforeach;?>

    </ul>
</div> -->

