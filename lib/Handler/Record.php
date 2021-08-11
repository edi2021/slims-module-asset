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

        // Store
        $Insert = $Builder->insert('asset_file', $data);

        if (empty($Builder->error))
        {
            $_SESSION['assetFile'][] = ['id' => $Builder->insert_id, 'filename' => $Upload->new_filename];
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