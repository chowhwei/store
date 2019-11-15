<?php

function classLoader($class)
{
    $path = str_replace('Chowhwei\\Store', 'src\\', $class);
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
    $file = __DIR__ . DIRECTORY_SEPARATOR .$path . '.php';
    if (file_exists($file)) {
        require_once $file;
    }else{
        echo $file;
        die();
    }
}
spl_autoload_register('classLoader');