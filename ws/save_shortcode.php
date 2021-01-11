<?php

$data = array(
    'shortcode_1' => $_POST['shortcode_1'], // shortcode name
   
);

$data = json_encode($data, JSON_UNESCAPED_UNICODE);

$message = null;
try {
    file_put_contents(dirname(__FILE__,2) . '/data/shortcode_name.json', $data );
    $message = 'exitoso';
} catch (\Throwable $th) {
    $message = 'error';
}

echo $message;
?>