<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-09 23:48:33
 * @modify date 2021-08-09 23:48:33
 * @desc [description]
 */

function dd($mix, $exit = true)
{
  echo '<pre>';
  var_dump($mix);
  echo '</pre>';
  
  if ($exit) exit;
}

function httpQuery(array $data)
{
  return '?' . http_build_query(array_unique(array_merge($_GET, $data)));
}

function isClassExists(string $className)
{
   return class_exists('SLiMSAssetmanager\\' . basename($className));
}