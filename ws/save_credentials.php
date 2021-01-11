<?php

$data = array(
    'credenciales_1' => $_POST['credenciales_1'], // usuario
    'credenciales_2' => $_POST['credenciales_2'], // pwd
    'credenciales_3' => $_POST['credenciales_3'] // schema
);

$data = json_encode($data, JSON_UNESCAPED_UNICODE);


try {
    file_put_contents(dirname(__FILE__,2) . '/data/credenciales_db.json', $data );
    $message = 'exitoso';
} catch (\Throwable $th) {
    $message = 'error';
}

// echo $message;
?>