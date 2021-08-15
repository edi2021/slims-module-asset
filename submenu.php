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
    ['Impor Item Asset', './modules/asset/index.php?view=importItem', 'Ekspor data item dalam bentuk spreadsheet'],
    ['Header', 'Laporan'],
    ['Laporan Asset', './modules/asset/index.php?view=report', 'Laporan data asset perpustakaan'],
    ['Header', 'Master File'],
    ['Penguasaan', './modules/asset/index.php?view=masterAuthorization', 'Daftar tipe penguasaan'],
    ['Sumber Perolehan', './modules/asset/index.php?view=masterSource', 'Daftar sumber perolehan'],
    ['Kondisi', './modules/asset/index.php?view=masterCondition', 'Daftar kondisi'],
    ['Status', './modules/asset/index.php?view=masterStatus', 'Status Item Asset'],
    ['Nomor Pola Asset', './modules/asset/index.php?view=codePatternList', 'Nomor Pola Asset'],
]);