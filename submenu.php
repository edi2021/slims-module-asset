<?php
$menu = [
    ['Header', 'Asset'],
    ['Daftar Asset', './modules/asset/index.php', 'Daftar seluruh aset yang telah di entri'],
    ['Tambah Asset Baru', './modules/asset/index.php?handler=Record&method=addForm&view=addAsset', 'Tambah Asset'],
    ['Header', 'Item Asset'],
    ['Daftar Item Asset', './modules/asset/index.php?view=itemList', 'Daftar Item Asset'],
    ['Daftar Penghapusan Item', './modules/asset/index.php?view=itemList&deleted=true', 'Daftar Penghapusan Item'],
];

// Tool
require __DIR__ . '/autoload.php';
$Tool = new SLiMSAssetmanager\Handler\Tool();

$menu = array_merge($menu, [['Header', 'Peralatan']], $Tool->getMenus(),[
    // Other Menu
    ['Ekspor Data Asset', './modules/asset/index.php?view=export', 'Ekspor data dalam bentuk spreadsheet'],
    ['Ekspor Item Asset', './modules/asset/index.php?view=exportItem', 'Ekspor data item dalam bentuk spreadsheet'],
    ['Impor Data Asset', './modules/asset/index.php?view=import', 'Ekspor data dalam bentuk spreadsheet'],
    ['Impor Item Asset', './modules/asset/index.php?view=importItem', 'Ekspor data item dalam bentuk spreadsheet']
]);