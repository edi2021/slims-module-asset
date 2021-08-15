<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-14 18:52:27
 * @modify date 2021-08-14 18:52:27
 * @desc [description]
 */

namespace SLiMSAssetmanager\View;

use \simbio_form_table_AJAX as Form;
use \simbio_form_element as FE;
use SLiMSAssetmanager\Ui\{Box,Grid};
use SLiMS\DB;

class masterSource
{
    private static function box()
    {
        // Set Box
        $Box = new Box($_SERVER['PHP_SELF'] . '?view=masterSource', 'GET');

        $Box
            ->setTitle('Sumber Perolehan Asset')
            ->setActionButton([
                    ['url' => $_SERVER['PHP_SELF'] . '?view=masterSource&addForm=true', 'label' => 'Tambah Data', 'class' => 'btn btn-primary'],
                    ['url' => $_SERVER['PHP_SELF'] . '?view=masterSource', 'label' => 'Daftar Asset', 'class' => 'btn btn-default']
                ])
            ->make();
    }
    private static function editData($id)
    {
        $Builder = DB::getInstance();

        $Process = $Builder->prepare('select * from asset_source where id = :id');
        $Process->execute(['id' => $id]);

        return ($Process->rowCount()) ? $Process->fetch(\PDO::FETCH_ASSOC) : [];
    }

    private static function addData()
    {
        $Data = self::editData($_POST['itemID'] ?? 0);

        // set form
        // set Form instance
        $Form = new Form('editMaster', $_SERVER['PHP_SELF'], 'post');

        // set prop
        $property = [
            'submit_button_attr' => 'name="saveData" value="' . ((count($Data)) ? __('Update') : __('Save')) . '" class="s-btn btn btn-default"',
            'table_attr' => 'id="dataList" cellpadding="0" cellspacing="0"',
            'table_header_attr' => 'class="alterCell font-weight-bold"',
            'table_content_attr' => 'class="alterCell2"',
            'edit_mode' => (bool)count($Data)
        ];

        // set fields
        $generalFields = [
            ['addTextField' => ['textarea', 'name', 'Sumber' . '*', setData('name', $Data), 'rows="1" class="form-control"', 'Isikan nama barang yang hendak di record']],
            ['addTextField' => ['textarea', 'detail', 'Keterangan', setData('detail', $Data), 'style="height: 150px" class="form-control"', 'Keterangan']]
        ];

        // Hidden Fields
        $hiddenFields = [
            ['addHidden' => ['handler', 'Master']],
            ['addHidden' => ['method', 'saveDataMaster']],
            ['addHidden' => ['id', (count($Data)) ? $Data['id'] : 0]],
            ['addHidden' => ['dest', 'source']]
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

        // Set Box
        self::box($Form, $Data);

        // make form
        echo $Form->printOut();
        exit;
    }

    public static function render($process = false)
    {
        if (isset($_GET['addForm'])) self::addData();
        if ($process) self::addData();

        // Set grid
        $props = [
                    'table_name' => 'Asset',
                    'table_attr' => 'id="dataList" class="s-table table"',
                    'table_header_attr' => 'class="dataListHeader" style="font-weight: bold;"',
                    'chbox_form_URL' => $_SERVER['PHP_SELF']
                ];

        $Grid = new Grid(DB::getInstance('mysqli'), $props, 20);

        // Make Grid
        $Grid
            ->setTableSpec('asset_source')
            ->setColumn('id, name as "Sumber", lastupdate')
            ->setCriteria('name LIKE "%{keyword}%"', true)
            ->mutation(0, 'callback{mutationMstSource}')
            ->make();

        // Box
        self::box();

        // show search info
        $Grid->getSearchInfo();

        // get result grid
        $Grid->getResult();
    }
}