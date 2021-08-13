<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-13 09:44:15
 * @modify date 2021-08-13 09:44:15
 * @desc [description]
 */

namespace SLiMSAssetmanager\Ui\Components;

use SLiMSAssetmanager\Ui\Box;

trait BoxComponent
{
    public static function boxRender()
    {
        $Box = new Box($_SERVER['PHP_SELF'] . '?view=itemList', 'GET');

        $Box
            ->setTitle('Tambah Item Asset Perpustakaan')
            ->setActionButton([
                    ['url' => $_SERVER['PHP_SELF'] . '?view=itemList', 'label' => 'Daftar Item Asset', 'class' => 'btn btn-primary']
                ])
            ->make();
    }
}
