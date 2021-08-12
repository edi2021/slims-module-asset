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
            exit('Handler not found!');
        }

        // Throw to view
        self::view();
    }

    private static function post($keys)
    {
        // Set post condition for Mutation or other reason
        postCondition($keys);

        if (isset($_POST[$keys]))
        {
            $handler = "SLiMSAssetmanager\Handler\\" . basename($_POST[$keys]);

            if (class_exists($handler))
            {
                (new $handler($_POST))->run();
                exit;
            }
            exit('Handler not found!');
        }

        // Throw to view
        self::view();
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

    private static function view()
    {
        if (isClassExists('View\\'.self::fetchKey('view')))
        {
            // Set view
            $View = "SLiMSAssetmanager\View\\".basename(self::fetchKey('view'));
            // Redering
            $View::render();
        }
        else
        {
            // Default view
            $View = "SLiMSAssetmanager\View\MainView";
            // Rendering
            $View::render();
        }
    }
}