<?php

declare(strict_types=1);
require_once __DIR__ . '/vendor/autoload.php';

(new FFIMe\FFIMe(__DIR__ . '/liberedis.so'))
    ->include(__DIR__ . '/eredis.h')
    ->codeGen('eredis\\eredis', __DIR__ . '/eredis.php');
require __DIR__ . '/eredis.php';

$ffi = new eredis\eredis();
$ffi->eredis_init();
$c = $ffi->eredis_create_client();

$str = 'A';
for ($i=0; $i<65533; $i++) {
    $str .= 'x';
} $str .= 'Z';

$ffi->eredis_prepare_request($c, 3, ["SET", "mykey", $str], null);
$ffi->eredis_execute($c);

settype($chunk_len, 'int');

$ffi->eredis_prepare_request($c, 2, ["GET", "mykey"], null);
//$ffi->eredis_prepare_request($c, 1, ["COMMAND"], null);  // ** this is what never returns completely !!
$ffi->eredis_execute($c);
$data = $ffi->eredis_read_reply_chunk($c, [&$chunk_len]);

echo("\n\$chunk_len -------------\n");
var_dump($chunk_len);
echo("\n\$chunk_len -------------\n");

echo("\n-------------\n");
var_dump($data->toString());


$data = $ffi->eredis_read_reply_chunk($c, [&$chunk_len]);
var_dump($data->toString(65551)); // ** why this works ?
echo("\n-------------\n");

