<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-10 13:14:32
 * @modify date 2021-08-10 13:14:32
 * @desc [description]
 */

namespace SLiMSAssetmanager\Http;

class Parse 
{
    public static function request($mixKeyToCapture)
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if (method_exists(__CLASS__, strtolower($method)))
        {
            self::{$method}($mixKeyToCapture);
        }
        else
        {
            Response::code(404);
            Response::setContent('text/plain');
            Response::text('Method tidak tersedia');
        }
    }

    private static function get($keys)
    {
       if (isset($_GET[$keys]))
       {
           $handler = "SLiMSAssetmanager\Handler\\" . basename($_GET[$keys]);

           if (class_exists($handler))
           {
               (new $handler($_GET))->run();
               exit;
           }
       }
    }

    private static function post($keys)
    {
        $_POST = (empty($_POST)) ? json_decode(file_get_contents('php://input'), TRUE) : $_POST;

        if (isset($_POST['tableName']))
        {
            $getHandler = explode('::', trim($_POST['tableName']));
            $_POST[$keys] = $getHandler[0];
            $_POST['tableName'] = $getHandler[1];
        }

        if (isset($_POST[$keys]))
        {
            $handler = "SLiMSAssetmanager\Handler\\" . basename($_POST[$keys]);

            if (class_exists($handler))
            {
                (new $handler($_POST))->run();
                exit;
            }
        }
    }

    public static function fetchKey(string $key)
    {
        $result = NULL;
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                if (isset($_POST[$key]))
                {
                    $result = $_POST[$key];
                }
                break;
            
            case 'GET':
                if (isset($_GET[$key]))
                {
                    $result = $_GET[$key];
                }
                break;
        }

        return $result;
    }
}