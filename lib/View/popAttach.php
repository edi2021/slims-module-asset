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

class popAttach
{
    private static function getData()
    {
        $Data = [];
        if (isset($_GET['id']) && !empty($_GET['id']))
        {
            $Builder = DB::getInstance();
            $State = $Builder->prepare('select * from asset_file where id = :id');
            $State->execute(['id' => $_GET['id']]);

            $Data = $State->fetch(\PDO::FETCH_ASSOC);
        }

        return $Data;
    }

    public static function render()
    {
        global $sysconf;

        $Data = self::getData();

        if (!isset($_SESSION['assetFile']))
        {
            $_SESSION['assetFile'] = [];
        }

        ob_start();
        // Set form instance
        $Form = new Form('itemForm', $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'], 'post');

        // set prop
        $property = [
            'submit_button_attr' => 'name="upload" value="' . __('Upload') . '" class="s-btn btn btn-default"',
            'table_attr' => 'id="dataList" cellpadding="0" cellspacing="0"',
            'table_header_attr' => 'class="alterCell"',
            'table_content_attr' => 'class="alterCell2"'
        ];

        $generalFields = [
            ['addTextField' => ['text', 'name', 'Nama File', setData('name', $Data), 'class="form-control"', 'Nama file']],
            ['addTextField' => ['textarea', 'description', 'Deskripsi', setData('description', $Data), 'class="form-control" style="height: 100px"', 'Deskripsi file']],
            ['addAnything' => ['File', FE::textField('file', 'filePendukung', '') . ' <br/> Nama file : ' .setData('path', $Data, function($Data) {
                $getFilename = explode(DS, $Data['path']);
                return $getFilename[array_key_last($getFilename)];
            })]]
        ];

        $hiddenFields = [
            ['addHidden' => ['handler', 'Record']],
            ['addHidden' => ['method', 'uploadAttachment']],
            ['addHidden' => ['state', count($Data) ? 'edit' : 'add']],
            ['addHidden' => ['id', count($Data) ? $Data['id'] : 0]]
        ];

        $fields = array_merge($hiddenFields, $generalFields);

        // register property
        foreach ($property as $prop => $value) {
            $Form->{$prop} = $value;
        }

        // Register field
        foreach ($fields as $field) {
            $method = array_key_first($field);
            $parameter = array_values($field)[0];
            call_user_func_array([$Form, $method], $parameter);
        }

        // make form
        echo $Form->printOut();

        // set content
        $content = ob_get_clean();
        // include the page template
        require __DIR__ .'/../Template/inIframe.tpl.php';
    }
}