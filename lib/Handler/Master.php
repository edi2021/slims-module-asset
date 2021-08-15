<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-14 18:57:27
 * @modify date 2021-08-14 18:57:27
 * @desc [description]
 */

namespace SLiMSAssetmanager\Handler;

// Load dependency
use SLiMSAssetmanager\Http\Parse;
use SLiMS\DB;
use \simbio_dbop as Builder;

class Master
{
    private function EditDataMaster()
    {
        $Request = Parse::fetchKey('view');

        if (isClassExists("View\\".$Request))
        {
            $View = 'SLiMSAssetmanager\View\\' . basename($Request);
            $View::render($edit = true);
        }
    }

    private function saveDataMaster()
    {
        $DB = DB::getInstance();

        if (isset($_POST['id']) && !empty($_POST['id']))
        {
            $Table = preg_replace('/[^A-Za-z0-0_]/i', '', $_POST['dest']);
            $Process = $DB->prepare('update asset_' . $Table . ' set name = :name, detail = :detail, lastupdate = :lastupdate where id = :id');
            $Process->execute(['name' => $_POST['name'], 'detail' => $_POST['detail'], 'id' => $_POST['id'], 'lastupdate' => date('Y-m-d H:i:s')]);
        }
        else
        {
            $Table = preg_replace('/[^A-Za-z0-0_]/i', '', $_POST['dest']);
            $Process = $DB->prepare('insert into asset_' . $Table . ' set name = :name, detail = :detail, inputdate = :inputdate, lastupdate = :lastupdate');
            $Process->execute(['name' => $_POST['name'], 'detail' => $_POST['detail'], 'inputdate' => date('Y-m-d H:i:s'), 'lastupdate' => date('Y-m-d H:i:s')]);
        }

        // error
        if (!$Process)
        {
            \utility::jsAlert('Galat : ' . $Process->errorInfo()); 
            exit;
        } 

        // Success
        \utility::jsAlert('Berhasil menyimpan data');
        simbioRedirect($_SERVER['PHP_SELF'] . '?view=master' . ucfirst($_POST['dest']));
    }

    public function run()
    {
        $Method = Parse::fetchKey('method');
        if (method_exists($this, $Method))
        {
            $this->{$Method}();
            exit;
        }
    }
}