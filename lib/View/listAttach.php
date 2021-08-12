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
use SLiMS\DB;

class listAttach
{
    private static function js()
    {
        
    }

    private static function getData()
    {
        if (isset($_GET['id']) && !empty($_GET['id']))
        {
            $Builder = DB::getInstance();
            $State = $Builder->prepare('select af.id, af.name from 
            asset_meta_file as amf inner join asset_file as af on af.id = amf.fileid
            where amf.assetid = :id');
            $State->execute(['id' => $_GET['id']]);

            $result = [];

            while ($Data = $State->fetch(\PDO::FETCH_ASSOC))
            {
                $result[] = ['filename' => $Data['name'], 'id' => $Data['id']];
            }

            $_SESSION['assetFile'] = $result;
        }
    }

    public static function render()
    {
        self::getData();

        ob_start();
        $HTML = '<div class="w-full">';
        foreach ((count($_SESSION['assetFile'])) ? $_SESSION['assetFile'] : [] as $index => $data) {
            $editUrl = $_SERVER['PHP_SELF'] . '?handler=Modal&method=popUp&view=popAttach&id=' . $data['id'];
            $HTML .= <<<HTML
                <div class="row">
                    <div class="col-3 my-2">
                        <a href="{$editUrl}" width="780" height="500" class="btn btn-primary mx-auto block notAJAX openPopUp" title="Edit data barang">Edit</a>
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