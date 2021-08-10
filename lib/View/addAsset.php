<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-10 18:48:33
 * @modify date 2021-08-10 18:48:33
 * @desc [description]
 */

namespace SLiMSAssetmanager\View;

// Load dependency
use \simbio_form_table_AJAX as Form;
use SLiMS\DB;
use SLiMSAssetmanager\Ui\Box;

class addAsset
{
    // set box
    private static function box()
    {
        // Set Box
        $Box = new Box($_SERVER['PHP_SELF'], 'GET');

        $Box
            ->setTitle('Asset Perpustakaan')
            ->setActionButton([
                    ['url' => $_SERVER['PHP_SELF'] . httpQuery(['handler' => 'Record', 'method' => 'addForm', 'view' => 'addAsset']), 'label' => 'Tambah Data', 'class' => 'btn btn-primary'],
                    ['url' => $_SERVER['PHP_SELF'], 'label' => 'Daftar Asset', 'class' => 'btn btn-default']
                ])
            ->make();
    }

    private static function getOption(string $table)
    {
        $DB = DB::getInstance();
        $Table = str_replace(['\'', '"', '`'], '', $table);
        $Data = $DB->query('select id,name from '.$Table);

        // Options
        $Options = [];
        $Options[] = ['0', 'Pilih'];
        while($result = $Data->fetch(\PDO::FETCH_NUM))
        {
            $Options[] = [$result[0], $result[1]];
        }

        return $Options;
    }

    // set render
    public static function render()
    {
        // set Form instance
        $Form = new Form('addAssetForm', $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'], 'post');

        // set prop
        $property = [
            'submit_button_attr' => 'name="saveData" value="' . __('Save') . '" class="s-btn btn btn-default"',
            'table_attr' => 'id="dataList" cellpadding="0" cellspacing="0"',
            'table_header_attr' => 'class="alterCell"',
            'table_content_attr' => 'class="alterCell2"'
        ];

        // set fields
        $fields = [
            ['addTextField' => ['textarea', 'name', 'Nama Barang' . '*', '', 'rows="1" class="form-control"', 'Isikan nama barang yang hendak di record']],
            ['addSelectList' => ['typeid', 'Jenis Barang', self::getOption('asset_type'), '', 'class="select2" data-src="' .$_SERVER['PHP_SELF'] . '?format=json&allowNew=true" data-src-table="Ajax::asset_type" data-src-cols="id:name"', 'Jenis barang']],
            ['addSelectList' => ['markid', 'Merek', self::getOption('asset_mark'), '', 'class="select2" data-src="' .$_SERVER['PHP_SELF'] . '?format=json&allowNew=true" data-src-table="Ajax::asset_mark" data-src-cols="id:name"', 'Jenis barang']],
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

        // Set Box
        self::box();

        // make form
        echo $Form->printOut();
    }
}