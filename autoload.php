<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-07-17 21:02:32
 * @modify date 2021-07-17 21:02:32
 * @desc [description]
 */

spl_autoload_register(function($class) {
    $class = str_replace('SLiMSAssetmanager\\', '', $class);
    $paths = explode('\\', $class);
    $fixPath = [];
    foreach ($paths as $index => $path) {
        if ($index === 0)
        {
            $fixPath[] = ucfirst($path);
        }
        else
        {
            $fixPath[] = $path;
        }
    }

    $truePath = __DIR__ . DS . 'lib' . DS . implode(DS, $fixPath) . '.php';

    if (file_exists($truePath))
    {
        include $truePath;
    }
});