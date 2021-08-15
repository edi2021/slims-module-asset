<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-13 18:05:00
 * @modify date 2021-08-13 18:05:00
 * @desc [description]
 */

define('INDEX_AUTH', '1');

// main system configuration
require '../../../sysconfig.inc.php';

// helper
require __DIR__ . DS . 'helper.php';
require __DIR__ . DS . 'asset.helper.php';
// autoload
require __DIR__ . DS . 'autoload.php';
// end dependency

tableCheck();

// Tools
SLiMSAssetmanager\Handler\Tool::getContent($_GET['name']);