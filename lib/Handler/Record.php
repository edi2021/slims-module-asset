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
        $LastInsertId = (isset($_POST['id'])) ? $Dbs->escape_string($_POST['id']) : $Dbs->escape_string($Builder->insert_id);

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

    private function getDataById(object $db, int $id, string $tableName, string $column = '*')
    {
        $tableName = preg_replace('/[^a-zA-Z_]*/', '', $tableName);
        $column = preg_replace('/[^a-zA-Z_,*]*/', '', $column);

        $q = $db->prepare('select ' . $column . ' from '. $tableName . ' where id = :id');
        $q->execute(['id' => $id]);

        return ($q->rowCount()) ? $q->fetch(\PDO::FETCH_ASSOC) : 0;
    }

    private function getItemById(object $db, int $id)
    {
        $q = $db->prepare('select itemcode from asset_item where assetid = :id');
        $q->execute(['id' => $id]);

        $result = [];

        while ($d = $q->fetch(\PDO::FETCH_NUM)) {
            $result[] = $d[0];
        }

        return ($q->rowCount()) ? '<' . implode('><', $result) . '>' : null;
    }

    private function insertItemImport(object $Builder, int $assetid, string $data)
    {
        if (empty($data)) return;

        $itemCodes = explode('><', trim(trim($data), '<>'));

        foreach ($itemCodes as $Code) {
            $Builder->insert('asset_item', ['itemcode' => $Code, 'assetid' => (int)$assetid]);
        }
    }

    private function insertNewMasterData(string $table, string $value, string $raw = '', array $rawExecute = [])
    {
        $DB = DB::getInstance();
        $tableName = preg_replace('/[^a-zA-Z_]*/', '', $table);

        if (empty($raw))
        {
            $Insert = $DB->prepare('insert ignore into ' . $tableName . ' set name = :name, inputdate = :inputdate, lastupdate = :lastupdate');
            $Insert->execute(['name' => $value, 'inputdate' => date('Y-m-d H:i:s'), 'lastupdate' => date('Y-m-d H:i:s')]);
        }
        else
        {
            $Insert = $DB->prepare('insert ignore into ' . $tableName . ' ' . $raw);
            $Insert->execute($rawExecute);
        }

        return $DB->lastInsertId() ?? $value;
    }

    private function export()
    {
        // set PHP time limit
        set_time_limit(0);
        // set ob implicit flush
        ob_implicit_flush();

        // set db instances
        $DB = DB::getInstance();

        $q = $DB->query('select id, name as Nama,typeid as Tipe,markid as Merek,authorizationid as Kepemilikan, image as Gambar,notes as Keterangan,inputdate,lastupdate from asset');

        if ($q->rowCount() < 1)
        {
            \utility::jsAlert('Tidak ada data yang dapat di eksport');
            exit;
        }
        
        // set header
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename="asset_export.csv"');

        // set character
        $oc = $_POST['openclose'];
        $sep = $_POST['separator'];

        // processing data
        $ColumnCount = 0;
        $Data = '';
        while ($d = $q->fetch(\PDO::FETCH_ASSOC))
        {
            if ($ColumnCount === 0)
            {
                $ColumnCount = count($d);
            }

            // Name
            $Data .= $oc . $d['Nama'] . $oc . $sep;

            // Tipe
            $Data .= $oc;
            $Data .= $this->getDataById($DB, $d['Tipe'], 'asset_type', 'name')['name'] ?? '?';
            $Data .= $oc . $sep;
           
            // Tipe
            $Data .= $oc;
            $Data .= $this->getDataById($DB, $d['Merek'], 'asset_mark', 'name')['name'] ?? '?';
            $Data .= $oc . $sep;

            // Tipe
            $Data .= $oc;
            $Data .= $this->getDataById($DB, $d['Kepemilikan'], 'asset_authorization', 'name')['name'] ?? '?';
            $Data .= $oc . $sep;

            // Image
            $Data .= $oc . $d['Gambar'] . $oc . $sep;
            // Keterangan
            $Data .= $oc . $d['Keterangan'] . $oc . $sep;
            // Input Data
            $Data .= $oc . $d['inputdate'] . $oc . $sep;
            // Last Update
            $Data .= $oc . $d['lastupdate'] . $oc . $sep;
            // Item
            $Data .= $oc . $this->getItemById($DB, $d['id']) . $oc . $sep;

            $Data .= "\n";
        }

        // Field
        $Column = '';
        // set column name
        for ($columnIdx=1; $columnIdx < $ColumnCount; $columnIdx++) { 
            $Meta = $q->getColumnMeta($columnIdx);
            $Column .= $oc . $Meta['name'] . $oc . $sep;
        }
        $Column = substr_replace($Column, "\n", -1);
        $Data = substr_replace($Data, '', -1);

        echo $Column . $Data;

    }

    private function exportItem()
    {
        // set PHP time limit
        set_time_limit(0);
        // set ob implicit flush
        ob_implicit_flush();

        // set db instances
        $DB = DB::getInstance();

        $q = $DB->query('select itemcode,locationid,placedetail,source,orderdate,receivedate,idorder,invoice,invoicedate,agentid,price,pricecurrency,inputdate,lastupdate from asset_item');

        if ($q->rowCount() < 1)
        {
            \utility::jsAlert('Tidak ada data yang dapat di eksport');
            exit;
        }
        
        // set header
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename="asset_item_export.csv"');

        // set character
        $oc = $_POST['openclose'];
        $sep = $_POST['separator'];

        // processing data
        $ColumnCount = 0;
        $Data = '';
        while ($d = $q->fetch(\PDO::FETCH_ASSOC))
        {
            if ($ColumnCount === 0)
            {
                $ColumnCount = count($d);
            }
            
            foreach ($d as $column => $value) {
                if (in_array($column, ['source','agentid']))
                {
                    $Data .= $oc . ($this->getDataById($DB, $d[$column], 'asset_' . str_replace('id', '', $column), 'name')['name'] ?? '?') . $oc . $sep;
                }
                else
                {
                    $Data .= $oc . $value . $oc . $sep;
                }
            }

            $Data .= "\n";
        }

        // Field
        $Column = '';
        // set column name
        for ($columnIdx=0; $columnIdx < $ColumnCount; $columnIdx++) { 
            $Meta = $q->getColumnMeta($columnIdx);
            $Column .= $oc . $Meta['name'] . $oc . $sep;
        }
        $Column = substr_replace($Column, "\n", -1);
        $Data = substr_replace($Data, '', -1);

        echo $Column . $Data;

    }

    private function import()
    {   
        global $sysconf;
        
        // set PHP time limit
        set_time_limit(0);
        // set ob implicit flush
        ob_implicit_flush();

        $DB = DB::getInstance('mysqli');
        $Builder = new Builder($DB);

        // set character
        $oc = $_POST['openclose'];
        $sep = $_POST['separator'];

        if (isset($_POST['upload']) && isset($_FILES['filetoupload']) AND $_FILES['filetoupload']['size'])
        {
            $tempDir = sys_get_temp_dir();
            $Upload = new Upload();
            $Upload->setAllowableFormat(['.csv']);
            $Upload->setMaxSize($sysconf['max_upload']*1024);
            $Upload->setUploadDir($tempDir);
            $Proces = $Upload->doUpload('filetoupload');

            if ($Proces != UPLOAD_SUCCESS)
            {
                \utility::jsAlert($Upload->error);
                exit;
            }

            $uploadedFile = $tempDir.DS.$_FILES['filetoupload']['name'];

            if (($handle = fopen($uploadedFile, "r")) !== FALSE) {
                $index = 0;
                $errorMsg = [];
                while (($data = fgetcsv($handle, 1000, $sep)) !== FALSE) {
                    if ($index > 0)
                    {
                        foreach ($data as $key => $value) {
                            if (!empty($value))
                            {
                                $data[$key] = $DB->escape_string($value);
                            }
                            else
                            {
                                unset($data[$key]);
                            }
                        }
    
                        // prepare data to insert
                        $cache = [];
                        $cache['name'] = $data[0];
                        $cache['typeid'] = $this->insertNewMasterData('asset_type', $data[1]);
                        $cache['markid'] = $this->insertNewMasterData('asset_mark', $data[2]);
                        $cache['authorizationid'] = $this->insertNewMasterData('asset_authorization', $data[3]);
                        $cache['image'] = $data[4];
                        $cache['notes'] = $data[5];
                        $cache['inputdate'] = $data[6];
                        $cache['lastupdate'] = date('Y-m-d H:i:s');
                        $cache['uid'] = $_SESSION['uid'];

                        $Insert = $Builder->insert('asset', $cache);

                        if (empty($Builder->error))
                        {
                            // insert item first
                            $this->insertItemImport($Builder, $Builder->insert_id, $data[8]);
                        }
                        else
                        {
                            $errorMsg[] = $Builder->error;
                        }
                    }
                    $index++;
                }

                if (count($errorMsg) === 0)
                {
                    \utility::jsAlert('Data berhasil disimpan.');
                }
                else
                {
                    \utility::jsAlert(implode("\n", $errorMsg));
                }
                fclose($handle);
            }

        }
    }

    private function importItem()
    {   
        global $sysconf;
        
        // set PHP time limit
        set_time_limit(0);
        // set ob implicit flush
        ob_implicit_flush();

        $DB = DB::getInstance('mysqli');
        $Builder = new Builder($DB);

        // set character
        $oc = $_POST['openclose'];
        $sep = $_POST['separator'];

        if (isset($_POST['upload']) && isset($_FILES['filetoupload']) AND $_FILES['filetoupload']['size'])
        {
            $tempDir = sys_get_temp_dir();
            $Upload = new Upload();
            $Upload->setAllowableFormat(['.csv']);
            $Upload->setMaxSize($sysconf['max_upload']*1024);
            $Upload->setUploadDir($tempDir);
            $Proces = $Upload->doUpload('filetoupload');

            if ($Proces != UPLOAD_SUCCESS)
            {
                \utility::jsAlert($Upload->error);
                exit;
            }

            $uploadedFile = $tempDir.DS.$_FILES['filetoupload']['name'];

            if (($handle = fopen($uploadedFile, "r")) !== FALSE) {
                $index = 0;
                $errorMsg = [];
                while (($data = fgetcsv($handle, 1000, $sep)) !== FALSE) {
                    if ($index > 0)
                    {
                        foreach ($data as $key => $value) {
                            if (!empty($value))
                            {
                                $data[$key] = $DB->escape_string($value);
                            }
                            else
                            {
                                unset($data[$key]);
                            }
                        }
                        
                        // prepare data to insert
                        $cache = [];
                        $cache['locationid'] = (isset($data[1])) ? $this->insertNewMasterData('mst_location', $data[1], 'location_name = :name, input_date = :inputdate, last_update = :lastupdate', ['name' => $data[1], 'inputdate' => date('Y-m-d'), 'lastupdate' => date('Y-m-d')]) : null;
                        $cache['source'] = $this->insertNewMasterData('asset_source', $data[3]);
                        $cache['agentid'] = ($data[9] === '?' ) ? 1 : 2;
                        $cache['placedetail'] = $data[2];
                        $cache['orderdate'] = $data[4];
                        $cache['receivedate'] = $data[5];
                        $cache['idorder'] = $data[6];
                        $cache['invoice'] = $data[7];
                        $cache['invoicedate'] = $data[8];
                        $cache['price'] = $data[10];
                        $cache['pricecurrency'] = $data[11];
                        $cache['inputdate'] = $data[12];
                        $cache['lastupdate'] = date('Y-m-d H:i:s');
                        $cache['uid'] = $_SESSION['uid'];

                        $Insert = $Builder->update('asset_item', $cache, 'itemcode="' . $DB->escape_string($data[0]) . '"' );

                        if (!empty($Builder->error))
                        {
                            $errorMsg[] = $Builder->error;
                        }
                    }
                    $index++;
                }

                if (count($errorMsg) === 0)
                {
                    \utility::jsAlert('Data berhasil disimpan.');
                }
                else
                {
                    \utility::jsAlert(implode("\n", $errorMsg));
                }
                fclose($handle);
            }

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