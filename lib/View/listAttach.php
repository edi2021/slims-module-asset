<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-11 07:29:42
 * @modify date 2021-08-11 07:29:42
 * @desc [description]
 */

namespace SLiMSAssetmanager\View;

use \simbio_form_table_AJAX as Form;
use \simbio_form_element as FE;

class listAttach
{
    private static function js()
    {
        
    }

    public static function render()
    {
        if (!isset($_SESSION['assetFile'])) exit;

        ob_start();
        $HTML = '<div class="w-full">';
        foreach ($_SESSION['assetFile'] as $index => $data) {
            $HTML .= <<<HTML
                <div class="row">
                    <div class="col-3 my-2">
                        <a href="#" class="btn btn-primary mx-auto block">Edit</a>
                        <a href="#" class="btn btn-danger mx-auto block">Delete</a>
                    </div>
                    <div class="col-9 my-3">
                        {$data['filename']}
                    </div>
                </div>
            HTML;
        }
        $HTML .= '</div>';

        echo $HTML;
        // set content
        $content = ob_get_clean();
        // include the page template
        require __DIR__ .'/../Template/inIframe.tpl.php';
    }
}