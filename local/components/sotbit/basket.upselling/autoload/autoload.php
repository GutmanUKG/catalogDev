<?php
    $files = scandir(__DIR__);

    foreach ($files as $file) {
        if ($file === 'autoload.php') {
            continue;
        }
        if (substr($file, -3, 3) === 'php') {
            require __DIR__ .'/'. $file ;
        }
    }