<?php
require 'vendor/autoload.php';

$files = glob('config/*.php');
foreach ($files as $file) {
    try {
        echo "Loading $file... ";
        $conf = require $file;
        echo "OK\n";
    } catch (\Throwable $e) {
        echo "ERROR loading $file: " . $e->getMessage() . "\n";
    }
}
