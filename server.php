<?php

$return = [
    'error' => 'Deu ruim!'
];

http_response_code(503);

echo json_encode($return);