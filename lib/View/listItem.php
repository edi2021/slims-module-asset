<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-11 07:29:42
 * @modify date 2021-08-20 18:01:53
 * @desc [description]
 */

namespace SLiMSAssetmanager\View;

use \simbio_form_table_AJAX as Form;
use \simbio_form_element as FE;
use SLiMSAssetmanager\Ui\Html;
use SLiMS\DB;

class listItem
{
    private static function js()
    {
        
    }

    private static function getData()
    {
        $result = [];
        if (isset($_GET['id']) && !empty($_GET['id']))
        {
            $Builder = DB::getInstance();
            $State = $Builder->prepare('select id, itemcode from asset_item where assetid = :id');
            $State->execute(['id' => $_GET['id']]);

            while ($Data = $State->fetch(\PDO::FETCH_ASSOC))
            {
                $result[] = ['itemcode' => $Data['itemcode'], 'id' => $Data['itemcode']];
            }
        }

        return $result;
    }

    public static function render()
    {
        ob_start();
        Html::$writeMode = 'return';
        $HTML = '<div class="w-full">';
        foreach ((count(self::getData())) ? self::getData() : [] as $index => $data) {
            $editUrl = $_SERVER['PHP_SELF'] . '?handler=Modal&method=popUp&view=editItemAsset&inPopUp=true&itemID=' . $data['id'];
            $HTML .= Html::write('div', 
                    Html::write('div', 
                        Html::write('a', 'Edit', ['href' => $editUrl, 'width' => 780, 'height' => 500, 'class' => 'btn btn-primary mx-auto block notAJAX openPopUp mr-2', 'title' => 'Edit data barang']) .
                        Html::write('a', 'Hapus', ['href' => '#', 'class' => 'btn btn-danger mx-1 block'])
                    ,['class' => 'col-3 my-2']) . 
                    Html::write('div', 
                        $data['itemcode']
                    ,['class' => 'col-9 my-3'])
                ,['class' => 'row']);
        }
        $HTML .= '</div>';

        echo $HTML;
        // set content
        $content = ob_get_clean();
        // include the page template
        require __DIR__ .'/../Template/inIframe.tpl.php';
    }
}