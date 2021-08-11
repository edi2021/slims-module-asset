<?php
/**
 * @Created by          : Drajat & Erwan
 * @Date                : 2021-08-09 22:31:57
 * @File name           : index.php
 */

// key to authenticate
if (!defined('INDEX_AUTH')) {
  define('INDEX_AUTH', '1');
}

// key to get full database access
define('DB_ACCESS', 'fa');

if (!defined('SB')) {
    // main system configuration
    require '../../../sysconfig.inc.php';
    // start the session
    require SB.'admin/default/session.inc.php';
}

// IP based access limitation
require LIB . 'ip_based_access.inc.php';
// set dependency
require SB.'admin/default/session_check.inc.php';
require SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO . 'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO . 'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO . 'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require SIMBIO . 'simbio_DB/simbio_dbop.inc.php';
require SIMBIO.'simbio_FILE/simbio_file_upload.inc.php';
// helper
require __DIR__ . DS . 'helper.php';
// autoload
require __DIR__ . DS . 'autoload.php';
// end dependency

// call namespace
use SLiMSAssetmanager\Ui\{Box,Grid};
use SLiMSAssetmanager\Http\{Parse,Response};

// privileges checking
$can_read = utility::havePrivilege('asset', 'r');

if (!$can_read) {
    die('<div class="errorBox">' . __('You are not authorized to view this section') . '</div>');
}

$page_title = 'Asset';

/* Action Area */
Parse::request('handler');

/* End Action Area */

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
            'table_name' => 'TableOfContent',
            'table_attr' => 'id="dataList" class="s-table table"',
            'table_header_attr' => 'class="dataListHeader" style="font-weight: bold;"',
            'chbox_form_URL' => $_SERVER['PHP_SELF']
        ];

$Grid = new Grid($dbs, $props, 5);
$Grid->canEdit = true;

// Make Grid
$Grid
    ->setTableSpec('asset')
    ->setColumn('id, name, image, lastupdate')
    ->setCriteria('name')
    ->make();

// show search info
$Grid->getSearchInfo();

// get result grid
$Grid->getResult();