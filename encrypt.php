<?php
function ssl_encrypt($item, $key, $cipher_method = 'aes-256-cfb', $options = []) {
    $iv = md5($key, true);
    $item = trim($item);
    $item = openssl_encrypt($item, $cipher_method, $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($item);
}

function ssl_decrypt($cipertext, $key, $cipher_method = 'aes-256-cfb', $options = []) {
    $iv = md5($key, true);
    $string = base64_decode($cipertext);
    return openssl_decrypt($string, $cipher_method, $key, OPENSSL_RAW_DATA, $iv);
}