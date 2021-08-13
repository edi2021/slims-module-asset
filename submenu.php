<?php

$menu = [
    ['Header', 'Asset'],
    ['Daftar Asset', './modules/asset/index.php', 'Daftar seluruh aset yang telah di entri'],
    ['Tambah Asset Baru', './modules/asset/index.php?handler=Record&method=addForm&view=addAsset', 'Tambah Asset'],
    ['Header', 'Item Asset'],
    ['Daftar Item Asset', './modules/asset/index.php?view=itemList', 'Daftar Item Asset'],
    ['Daftar Penghapusan Item', './modules/asset/index.php?view=itemList&deleted=true', 'Daftar Penghapusan Item']
];