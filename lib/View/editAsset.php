<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-10 18:48:33
 * @modify date 2021-08-12 09:38:46
 * @desc [description]
 */

namespace SLiMSAssetmanager\View;

// Load dependency
use \simbio_form_table_AJAX as Form;
use \simbio_form_element as FE;
use SLiMS\DB;
use SLiMSAssetmanager\Ui\Box;
use SLiMSAssetmanager\View\addAsset;

class editAsset extends addAsset
{
    private static function data($id)
    {
        $Builder = DB::getInstance();

        $Data = $Builder->prepare('select id,name,typeid,markid,authorizationid,notes,lastupdate from asset where id = :id');
        $Data->execute(['id' => $id]);

        return $Data->fetch(\PDO::FETCH_ASSOC);
    }

    // set render
    public static function render($Data = [])
    {
        parent::render(self::data($_POST['itemID']));
    }
}