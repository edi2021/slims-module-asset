<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-13 16:23:09
 * @modify date 2021-08-13 16:23:09
 * @desc [description]
 */

namespace SLiMSAssetmanager\Handler;

class Tool
{
    private $listPlugins = [];
    private static $menus = [];

    public static function setMenu()
    {
        $argument = func_get_args();
        self::$menus[] = [$argument[0], './modules/asset/tool.php?name='.$argument[1]];
    }

    private function scan()
    {
        $Path = __DIR__ . DS . '..' . DS . 'Tool';
        
        foreach ( array_diff(scandir($Path), ['.','..']) ?? [] as $Dir) {
            if (!preg_match('/\s/i', $Dir))
            {
                $PluginDir = array_diff(scandir($Path . DS . $Dir), ['.','..']);
                $File = array_values(array_filter($PluginDir, function($file){
                    if (!is_dir($file) && preg_match('/.plugin.php/i', $file)) return true;
                }))[0];

                $this->listPlugins[$Dir] = ['dir' => $Path . DS . $Dir . DS, 'file' => $File];
            }
        }

        return $this;
    }

    private function load()
    {
        foreach ($this->listPlugins as $Plugin) {
            include_once $Plugin['dir'] . $Plugin['file'];
        }

        return self::$menus;
    }

    public function getMenus()
    {
        return $this->scan()->load();
    }

    public static function getContent($name)
    {
        global $sysconf,$dbs;

        $Path = __DIR__ . DS . '..' . DS . 'Tool' .DS . basename($name) . DS . 'index.php';

        if (file_exists($Path))
        {
            include_once $Path;
        }
        else
        {
            echo $Path;
        }
    }

    public function run()
    {
        exit('Just for plugin :)');
    }
}