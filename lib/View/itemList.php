<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-12 20:22:20
 * @modify date 2021-08-12 20:22:20
 * @desc [description]
 */

namespace SLiMSAssetmanager\View;

use SLiMSAssetmanager\Ui\{Box,Grid};
use SLiMS\DB;

class itemList
{
    public static function render()
    {
        // Set Box
        $isDeleteList = (isset($_GET['deleted']) ? '&deleted=true' : NULL);
        $Box = new Box($_SERVER['PHP_SELF'] . '?view=itemList' . $isDeleteList, 'GET');

        if (!is_null($isDeleteList))
        {
            $Box->setCustomField('<input type="hidden" name="deleted" value="true"/>');
        }

        $Box
            ->setTitle('Item Asset Perpustakaan ' . ((!is_null($isDeleteList)) ? 'Terhapus' : NULL))
            ->setActionButton([
                    ['url' => $_SERVER['PHP_SELF'] . '?view=itemList' . $isDeleteList, 'label' => 'Daftar Item Asset', 'class' => 'btn btn-primary']
                ])
            ->make();

        // Set grid
        $props = [
                    'table_name' => 'ItemAsset',
                    'table_attr' => 'id="dataList" class="s-table table"',
                    'table_header_attr' => 'class="dataListHeader" style="font-weight: bold;"',
                    'chbox_form_URL' => $_SERVER['PHP_SELF']
                ];

        $Grid = new Grid(DB::getInstance('mysqli'), $props, 20);
        $Grid->canEdit = (!is_null($isDeleteList) ? false : true);
        $Grid->setSQLCriteria('deleted = ' . (!is_null($isDeleteList) ? 2 : 1));

        if (!$Grid->canEdit) $Grid->invisible_fields = [0];

        $Grid->debug = true;

        // Make Grid
        $Grid
            ->setTableSpec('asset_item as ai')
            ->setColumn('ai.itemcode, ai.itemcode as "Kode QR", 
                         a.name AS "Nama Aset", ai.locationid as "Lokasi", 
                         ai.lastupdate as "Terakhir Diperbaharui"')
            ->setJoin('asset as a', 'a.id = ai.assetid', 'inner')
            ->setCriteria(' and (ai.itemcode LIKE "%{keyword}%" or a.name LIKE "%{keyword}%")', true)
            ->mutation(0, 'callback{mutationZeroItem}')
            ->mutation(2, 'callback{mutationSetItemLabel}')
            ->make();

        // show search info
        $Grid->getSearchInfo();

        // get result grid
        $Grid->getResult();
    }
}