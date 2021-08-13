<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-10 18:05:27
 * @modify date 2021-08-10 18:05:27
 * @desc [description]
 */

namespace SLiMSAssetmanager\Handler;

// Load dependency
use SLiMSAssetmanager\Http\Parse;
use SLiMS\DB;
use \simbio_dbop as Builder;
use \simbio_file_upload as Upload;

class Record
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    private function addForm()
    {
        if (isClassExists('View\\' . basename($_GET['view'])))
        {
            // set view namespace
            $View = 'SLiMSAssetmanager\View\\' . basename($_GET['view']);
            // render
            $View::render();
        }
    }

    private function updateItemData()
    {
        if (isset($_POST['id']) && empty($_POST['id']))
        {
            \utility::jsAlert('Request tidak valid!');
            exit;
        }

        $Builder = new Builder($Instance = DB::getInstance('mysqli'));

        // set allow data
        $AllowData = ['locationid','placedetail','deleted','source','orderdate','receivedate','idorder','invoice','invoicedate','agentid','price','price'];

        // Id
        $Id = $Instance->escape_string($_POST['id']);

        // Filtering
        $Data = [];
        foreach ($AllowData as $data) {
            if (isset($_POST[$data]))
            {
                $Data[$data] = $Instance->escape_string($_POST[$data]);
            }
        }

        $Data['lastupdate'] = date('Y-m-d H:i:s');
        
        $Update = $Builder->update('asset_item', $Data, 'id='.$Id);

        if (empty($Builder->error))
        {
            \utility::jsAlert('Data berhasil diperbaharui');
            simbioRedirect($_SERVER['PHP_SELF'] . '?view=itemList');
            exit;
        }

        \utility::jsAlert('Data tidak berhasil di update : ' . $Builder->error);
    }

    private function deleteAsset()
    {
        $Builder = new Builder($Instance = DB::getInstance('mysqli'));

        if (isset($_POST['itemID']) && count($_POST['itemID']) > 0)
        {
            foreach ($_POST['itemID'] as $id) {
                $id = substr($Instance->escape_string($id), 0,1);
                $Builder->delete('asset', $id);
            }

            \utility::jsAlert('Data berhasil dihapus!');
            simbioRedirect($_SERVER['PHP_SELF']);
        }
    }

    private function EditData()
    {
        if (isClassExists('View\\' . basename($_POST['view'])))
        {
            // set view namespace
            $View = 'SLiMSAssetmanager\View\\' . basename($_POST['view']);
            // render
            $View::render();
        }
    }

    private function saveItem(object $Builder, int $AssetId, int $NextNumber, array $Pattern, object $callback)
    {
        // Set Item
        $Item = [];
        
        // set allowed data
        $allowData = ['locationid','placedetail','deleted','source','orderdate','receivedate','idorder','invoice','invoicedate','agentid','price','pricecurrency'];

        foreach ($allowData as $key) {
            if (isset($_POST[$key]) && !empty($_POST[$key]))
            {
                $Item[$key] = $callback($_POST[$key]);
            }
        }

        // Infor data
        $Item['inputdate'] = date('Y-m-d H:i:s');
        $Item['lastupdate'] = date('Y-m-d H:i:s');
        $Item['uid'] = (int)$_SESSION['uid'];
        $Item['assetid'] = $AssetId;

        for ($i = ($NextNumber + 1); $i <= ($NextNumber + $_POST['totalItems']); $i++) { 
            $Item['itemcode'] = $Pattern['data'][0] . sprintf('%0' . $Pattern['data'][2] . 'd', $i) . $Pattern['data'][1];
            $Builder->insert('asset_item', $Item);
            echo $Builder->getSQL();
        }

    }

    private function saveData()
    {
        global $sysconf;

        // Create builder and DB instance
        $Dbs = DB::getInstance('mysqli');
        $Builder = new Builder($Dbs);
        $Data = [];

        // Set data
        $allowData = ['name','typeid','markid','authorizationid','notes'];

        foreach ($allowData as $key) {
            if (isset($_POST[$key]))
            {
                $Data[$key] = $Dbs->escape_string($_POST[$key]);
            }
        }

        // upload process
        if (isset($_FILES['image']) && $_FILES['image']['size'])
        {
            $NewName = str_replace(' ', '-', strtolower($_POST['name']));
            $Upload = new Upload();
            $Upload->setAllowableFormat($sysconf['allowed_images']);
            $Upload->setMaxSize($sysconf['max_upload']*1024);
            $Upload->setUploadDir(SB. 'images' . DS . 'docs');
            $Process = $Upload->doUpload('image', $NewName);

            if ($Process === UPLOAD_SUCCESS)
            {
                // let prepare to store data
                $Data['image'] = $Upload->new_filename;
                \Utility::jsAlert('Upload success');
            }
            else
            {
                \Utility::jsAlert('Galat : ' . $Upload->error);
                exit;
            }
        }

        // set id from options element
        $Data['typeid'] = getOptionsId($Dbs, 'asset_type', str_replace('NEW:', '', $Data['typeid']));
        $Data['markid'] = getOptionsId($Dbs, 'asset_mark', str_replace('NEW:', '', $Data['markid']));
        $Data['authorizationid'] = getOptionsId($Dbs, 'asset_authorization', str_replace('NEW:', '', $Data['authorizationid']));

        // set date data
        $Data['inputdate'] = date('Y-m-d H:i:s');
        $Data['lastupdate'] = date('Y-m-d H:i:s');
        $Data['uid'] = (int)$_SESSION['uid'];

        // set action
        $action = 'insert';
        $Param = ['asset', $Data];
        if (isset($_POST['id']) && !empty($_POST['id']))
        {
            $Id = $Dbs->escape_string($_POST['id']);
            $action = 'update';
            $Param = ['asset', $Data, 'id='.$Id];
        }

        // Insert
        $Insert = call_user_func_array([$Builder, $action], $Param);

        if (!empty($Builder->error))
        {
            \utility::jsAlert('Galat : '. $Builder->error);
            exit;
        }

        // Set last insert id
        $LastInsertId = (isset($Data['id'])) ? $Dbs->escape_string($_POST['id']) : $Dbs->escape_string($Builder->insert_id);

        // Get pattern data
        $Pattern = $Dbs->query("SELECT * FROM `setting`WHERE setting_name = 'assetPattern'");

        if ($Pattern->num_rows && (isset($_POST['totalItems']) && !empty($_POST['totalItems'])))
        {
            $PatternData = json_decode($Pattern->fetch_assoc()['setting_value'], TRUE);
            $PatternToUse = array_values(array_filter($PatternData, function($pattern){
                if ($pattern['label'] === $_POST['itemCodePattern'])
                {
                    return true;
                }
            }));

            if (count($PatternToUse))
            {
                self::saveItem($Builder, $LastInsertId, countPattern($Dbs, $PatternToUse[0]), $PatternToUse[0], function($mix) use($Dbs) {
                    return $Dbs->escape_string($mix);
                });
            }
        }

        // Save file
        if (isset($_SESSION['assetFile']) && count($_SESSION['assetFile']) > 0)
        {
            $Builder->delete('asset_meta_file', 'assetid='.$LastInsertId);
            foreach ($_SESSION['assetFile'] as $index => $data) {
                $Builder->insert('asset_meta_file', ['fileid' => $data['id'], 'assetid' => $LastInsertId, 'inputdate' => date('Y-m-d H:i:s')]);
            }
        }

        // unset $_SESSION;
        unset($_SESSION['assetFile']);

        \Utility::jsAlert('Data berhasil disimpan.');
        simbioRedirect($_SERVER['PHP_SELF']);
    }

    private function savePattern()
    {
        $Dbs = DB::getInstance('mysqli');
        $Builder = new Builder($Dbs);

        // get pattern data
        $patternData = $Dbs->query('select setting_value from `setting` where setting_name = "assetPattern"');

        $pattern = [];
        if ($patternData->num_rows > 0)
        {
            $pattern = json_decode($patternData->fetch_row()[0], TRUE);
        }

        // set new data
        $prefix = $Dbs->escape_string($_POST['prefix']);
        $suffix = $Dbs->escape_string($_POST['suffix']);
        $numLong = (int)$_POST['numLong'];
        $label = $prefix . sprintf('%0' . $numLong . 'd', 0) . $suffix;
        $newPattern = ['data' => [$prefix, $suffix, $numLong], 'label' => $label];
        $pattern = json_encode(array_merge($pattern, [$newPattern]));
        
        // store it
        if ($patternData->num_rows === 0)
        {
            $Builder->insert('setting', ['setting_name' => 'assetPattern', 'setting_value' => $pattern]);
        }
        else
        {
            $Builder->update('setting', ['setting_value' => $pattern], 'setting_name="assetPattern"');
        }

        if (empty($Builder->error))
        {
            \utility::jsAlert('Pola berhasil disimpan.');
            echo <<<HTML
                <script>
                    let select = parent.document.querySelector('#itemCodePattern')
                    select.innerHTML += '<option value="{$label}">{$label}</option>'
                </script>
            HTML;
        }
        else
        {
            \utility::jsAlert('Galat : ' . $Builder->error);
        }
    }

    private function uploadAttachment()
    {
        global $sysconf;

        $Dbs = DB::getInstance('mysqli');
        $Builder = new Builder($Dbs);
        $data = [];

        // Common data
        $data['name'] = $Dbs->escape_string($_POST['name']);
        $data['description'] = $Dbs->escape_string($_POST['description']);
        $data['lastupdate'] = date('Y-m-d H:i:s');
        $data['uid'] = $_SESSION['uid'];

        if (isset($_POST['upload']) && !empty($_POST['name']) && isset($_FILES['filePendukung']) AND $_FILES['filePendukung']['size'])
        {
            $Upload = new Upload();
            $Upload->setAllowableFormat($sysconf['allowed_file_att']);
            $Upload->setMaxSize($sysconf['max_upload']*1024);
            $Upload->setUploadDir(REPOBS);

            $Process = $Upload->doUpload('filePendukung', md5($_POST['name'] . '-' . date('Y-m-d H:i:s')));

            if ($Process === UPLOAD_SUCCESS)
            {
                // let prepare to store data
                $data['path'] = REPOBS . $Upload->new_filename;
                $data['inputdate'] = date('Y-m-d H:i:s');
            }
        }

        $action = 'insert';
        $Param = ['asset_file', $data];
        if (isset($_POST['state']) && $_POST['state'] === 'edit' && !empty($_POST['id']))
        {
            $action = 'update';
            $Id = $Dbs->escape_string($_POST['id']);
            $Param = ['asset_file', $data, "id=".$Id];
        }

        // Store
        $Process = call_user_func_array([$Builder, $action], $Param);

        $LastId = ($_POST['state'] === 'edit') ? $Dbs->escape_string($_POST['id']) : $Builder->insert_id;

        if (empty($Builder->error))
        {

            // if (!isset($_SESSION['assetFile'][$LastId]))
            // {
                $_SESSION['assetFile'][$LastId] = ['id' => $LastId, 'filename' => $Upload->new_filename];
            // }

            \Utility::jsAlert('Data berhasil diupload dan disimpan');
            echo <<<HTML
                <script>
                    let doc = parent.parent.document
                    doc.querySelector('#cboxClose').click()
                    doc.querySelector('#attachIframe').contentWindow.location.reload()
                </script>
            HTML;
        }
        else
        {
            \Utility::jsAlert('Galat : ' . $Builder->error);
        }
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