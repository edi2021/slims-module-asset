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

class popAttach
{
    public static function render()
    {
        global $sysconf;

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

        $fields = [
            ['addHidden' => ['handler', 'Record']],
            ['addHidden' => ['method', 'uploadAttachment']],
            ['addTextField' => ['text', 'name', 'Nama File', '', 'class="form-control"', 'Nama file']],
            ['addTextField' => ['textarea', 'description', 'Deskripsi', '', 'class="form-control" style="height: 100px"', 'Deskripsi file']],
            ['addAnything' => ['File', FE::textField('file', 'filePendukung', '')]]
        ];

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