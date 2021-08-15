<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-12 20:54:20
 * @modify date 2021-08-12 20:54:20
 * @desc [description]
 */

namespace SLiMSAssetmanager\View;

use \simbio_form_table_AJAX as Form;
use \simbio_form_element as FE;
use SLiMSAssetmanager\Ui\Box;
use SLiMS\DB;
use SLiMSAssetmanager\View\addAsset;

class editItemAsset extends addAsset
{   
    private static function getData()
    {
        $Result = [];
        if (isset($_POST['itemID']))
        {
            $Builder = DB::getInstance();

            $Process = $Builder->prepare('select * from asset_item where itemcode = :itemcode');
            $Process->execute(['itemcode' => $_POST['itemID']]);

            $Result = $Process->fetch(\PDO::FETCH_ASSOC);
        }

        return $Result;
    }

    public static function render($Data = [])
    {
        global $sysconf;

        $Data = self::getData();

        // Set Box
        if (!isset($_GET['inPopUp']))
        {
            $Box = new Box($_SERVER['PHP_SELF'] . '?view=itemList', 'GET');

            $Box
                ->setTitle('Edit Item Asset Perpustakaan')
                ->setActionButton([
                        ['url' => $_SERVER['PHP_SELF'] . '?view=itemList', 'label' => 'Daftar Item Asset', 'class' => 'btn btn-primary']
                    ])
                ->make();
        }

        // set Form instance
        $Form = new Form('addAssetForm', $_SERVER['PHP_SELF'], 'post');

        // set prop
        $property = [
            'submit_button_attr' => 'name="saveData" value="' . ((count($Data)) ? __('Update') : __('Save')) . '" class="s-btn btn btn-default"',
            'table_attr' => 'id="dataList" cellpadding="0" cellspacing="0"',
            'table_header_attr' => 'class="alterCell"',
            'table_content_attr' => 'class="alterCell2"',
            'edit_mode' => (bool)count($Data)
        ];

        // set fields
        $generalFields = [
            ['addAnything' => ['Kode Item', '<b>'.setData('itemcode', $Data).'</b>']],
            ['addAnything' => ['Lokasi', FE::selectList('locationid', self::getRawOptions(__('Location'), 'location_id, location_name', 'mst_location'), setData('locationid', $Data), 'class="form-control col-4"')]],
            ['addAnything' => ['Penempatan', FE::textField('text', 'placedetail', setData('placedetail', $Data), 'class="form-control col-4"')]],
            ['addAnything' => ['Status', FE::selectList('deleted', [['1', 'Ada'], ['2', 'Di Hapus']], setData('deleted', $Data), 'class="form-control col-4"')]],
            ['addAnything' => ['Sumber', FE::selectList('source', [['1', __('Buy')], ['2', __('Prize/Grant')]], setData('source', $Data), 'class="form-control col-4"')]],
            ['addAnything' => ['Tanggal Order', FE::dateField('orderdate', setData('orderdate', $Data), 'class="form-control"')]],
            ['addAnything' => ['Tanggal Diterima', FE::dateField('receivedate', setData('receivedate', $Data), ' class="form-control col-12"')]],
            ['addAnything' => ['Nomor Order', FE::textField('text', 'idorder', setData('idorder', $Data), 'class="form-control"')]],
            ['addAnything' => ['Faktur', FE::textField('text', 'invoice', setData('invoice', $Data), 'class="form-control col-4"')]],
            ['addAnything' => ['Tanggal Faktur', FE::dateField('invoicedate', setData('invoicedate', $Data), ' class="form-control col-12"')]],
            ['addAnything' => ['Agen', FE::selectList('agentid', self::getOptions('asset_agent'), setData('agentid', $Data), 'class="form-control col-4"')]],
            ['addAnything' => ['price', FE::textField('text', 'price', setData('price', $Data), 'class="form-control col-3"') . FE::selectList('pricecurrency', $sysconf['currencies'], setData('pricecurrency', $Data), 'class="form-control col-2"')]]
        ];

        // Hidden Fields
        $hiddenFields = [
            ['addHidden' => ['handler', 'Record']],
            ['addHidden' => ['method', 'updateItemData']],
            ['addHidden' => ['id', (count($Data)) ? $Data['id'] : 0]]
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

        // make form
        echo $Form->printOut();
    }
}