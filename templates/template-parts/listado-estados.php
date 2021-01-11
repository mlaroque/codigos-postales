<?php
    require_once( ABSPATH . 'wp-admin/includes/post.php' ); 
    include dirname(__DIR__, 2) . '/functions/utils.php';
    include dirname(__DIR__, 2).'/functions/get_connection.php'; 

    global $conn;
?>

<h2 class="text-center">Estados</h2>

<div class="table-responsive CPtableBox shadow">
    <table class="CPtable">
        <thead>
            <tr>
                <th>Estado</th>
                <th>Rango de Códigos Postales</th>
            </tr>
        </thead>
        <tbody>

             <?php 
                $sql = "SELECT estado, GROUP_CONCAT(DISTINCT cp ORDER BY cp ASC separator ',') as rango from LCMN_CODIGOS_POSTALES GROUP BY estado ";
                foreach($conn->query($sql) as $row):
                    $estado = $row['estado'];
            ?>

                <tr>
                    <td class="CPestado" data-th="Estado"><a href="<?php echo CP_custom_get_permalink($estado, 'estado'); ?>"> <?php echo $row['estado']; ?> </a></td>
 
                    <td class="CPcp" data-th="Rango de Códigos Postales"> <?php echo CP_conver_cp_str_to_links($row['rango']);?></td>
                </tr>

            <?php endforeach;?>
            
        </tbody>
    </table>
</div>