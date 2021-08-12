<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-12 20:09:11
 * @modify date 2021-08-12 20:09:11
 * @desc [description]
 */

namespace SLiMSAssetmanager\View;

use SLiMSAssetmanager\Ui\{Box,Grid};
use SLiMS\DB;

class MainView
{
    public static function render()
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

        // Set grid
        $props = [
                    'table_name' => 'Asset',
                    'table_attr' => 'id="dataList" class="s-table table"',
                    'table_header_attr' => 'class="dataListHeader" style="font-weight: bold;"',
                    'chbox_form_URL' => $_SERVER['PHP_SELF']
                ];

        $Grid = new Grid(DB::getInstance('mysqli'), $props, 20);
        $Grid->canEdit = true;

        // Make Grid
        $Grid
            ->setTableSpec('asset')
            ->setColumn('id, name, image, lastupdate')
            ->setCriteria('name LIKE "%{keyword}%"', true)
            ->mutation(0, 'callback{mutationZero}')
            ->make();

        // show search info
        $Grid->getSearchInfo();

        // get result grid
        $Grid->getResult();
    }
}