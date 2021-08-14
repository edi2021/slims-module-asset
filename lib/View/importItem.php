<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-14 12:46:56
 * @modify date 2021-08-14 12:46:56
 * @desc [description]
 */

namespace SLiMSAssetmanager\View;
// Load dependency
use \simbio_form_table_AJAX as Form;
use \simbio_form_element as FE;
use SLiMS\DB;
use SLiMSAssetmanager\Ui\Box;

class importItem
{
    private static function box()
    {
        // Set Box
        $Box = new Box($_SERVER['PHP_SELF'], 'GET');
        $Box->disableForm = true;

        $Box
            ->setTitle('Import item Asset')
            ->setActionButton([
                    ['url' => $_SERVER['PHP_SELF'] . httpQuery(['handler' => 'Record', 'method' => 'addForm', 'view' => 'addAsset']), 'label' => 'Tambah Data', 'class' => 'btn btn-primary'],
                    ['url' => $_SERVER['PHP_SELF'], 'label' => 'Daftar Asset', 'class' => 'btn btn-default']
                ])
            ->make();

    }
    public static function render()
    {
        // set Form instance
        $Form = new Form('addAssetForm', $_SERVER['PHP_SELF'], 'post');

        // set property
        $property = [
            'submit_button_attr' => 'name="upload" value="' . __('Import') . '" class="s-btn btn btn-default"',
            'table_attr' => 'id="dataList" cellpadding="0" cellspacing="0"',
            'table_header_attr' => 'class="alterCell font-weight-bold"',
            'table_content_attr' => 'class="alterCell2"'
        ];

        // set fields
        $generalFields = [
            ['addTextField' => ['text', 'openclose', 'Pembukan dan Penutup', ''.htmlentities('"').'', 'style="width: 20%" class="form-control"', 'Penutup Setiap Data']],
            ['addTextField' => ['text', 'separator', 'Pemisah', ',', 'style="width: 20%" class="form-control"', 'Pemisah Antar Data']],
            ['addAnything' => ['File', FE::textField('file', 'filetoupload', '')]]
        ];

        // Hidden fields
        $hiddenFields = [
            ['addHidden' => ['handler', 'Record']],
            ['addHidden' => ['method', 'importItem']],
        ];

        // Mix in
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

        // Set box
        self::box();

        // make form
        echo $Form->printOut();
    }
}