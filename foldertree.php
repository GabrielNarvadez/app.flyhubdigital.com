<?php
function printTree($dir, $prefix = '') {
    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        echo $prefix . $item . "\n";
        if (is_dir("$dir/$item")) {
            printTree("$dir/$item", $prefix . '    ');
        }
    }
}
header('Content-Type: text/plain');
printTree(__DIR__);

