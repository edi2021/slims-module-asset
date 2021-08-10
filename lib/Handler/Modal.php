<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-10 18:05:27
 * @modify date 2021-08-10 18:05:27
 * @desc [description]
 */

namespace SLiMSAssetmanager\Handler;

class Modal
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function popUp()
    {
        if (isClassExists('View\\' . basename($_GET['view'])))
        {
            // set view namespace
            $View = 'SLiMSAssetmanager\View\\' . basename($_GET['view']);
            // render
            $View::render();
        }
    }

    public function run()
    {
        if (method_exists($this, $_GET['method']))
        {
            $this->{$_GET['method']}();
            exit;
        }
    }
}