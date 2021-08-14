<?php
/**
 * Plugin Name: Label Qrcode Color Slims
 * Plugin URI: https://github.com/drajathasan/label_mixcode_color_slims
 * Description: Plugin pengganti label_barcode_color_slims dengan fitur baru dan support SLiMS 9 terbaru
 * Version: 1.0.0
 * Author: Drajat Hasan
 * Author URI: https://github.com/drajathasan/
 */

// get plugin instance
$Dir = explode(DS, __DIR__);

// registering menus
SLiMSAssetmanager\Handler\Tool::setMenu('Cetak Kode Qr', $Dir[array_key_last($Dir)]);
