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

class editItemAsset
{
    public static function render()
    {
        // Set Box
        if (!isset($_GET['inPopUp']))
        {
            $Box = new Box($_SERVER['PHP_SELF'] . '?view=itemList', 'GET');

            $Box
                ->setTitle('Tambah Item Asset Perpustakaan')
                ->setActionButton([
                        ['url' => $_SERVER['PHP_SELF'] . '?view=itemList', 'label' => 'Daftar Item Asset', 'class' => 'btn btn-primary']
                    ])
                ->make();
        }

        // set Form instance
        $Form = new Form('addAssetForm', $_SERVER['PHP_SELF'], 'post');
        
    }
}