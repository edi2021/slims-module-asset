<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-06-29 08:16:33
 * @modify date 2021-06-29 08:16:33
 * @desc [description]
 */

isDirect();

$max_print = 50;

if (isset($_POST['itemID']) AND !empty($_POST['itemID']) AND isset($_POST['itemAction'])) {
    if (!is_array($_POST['itemID'])) {
      // make an array
      $_POST['itemID'] = array((integer)$_POST['itemID']);
    }
    // loop array
    if (isset($_SESSION['qrcodes'])) {
      $print_count = count($_SESSION['qrcodes']);
    } else {
      $print_count = 0;
    }
    // create AJAX request
    echo '<script type="text/javascript">';
    // loop array
    foreach ($_POST['itemID'] as $itemID) {
      if ($print_count == $max_print) {
        $limit_reach = true;
        break;
      }
      if (isset($_SESSION['qrcodes'][$itemID])) {
        continue;
      }
      if (!empty($itemID)) {
        // add to sessions
        $_SESSION['qrcodes'][$itemID] = $itemID;
        $print_count++;
      }
    }
    echo 'top.document.querySelector(\'#queueCount\').innerHTML = \''.$print_count.'\'';
    echo '</script>';
    // update print queue count object
    sleep(2);
    if (isset($limit_reach)) {
      utility::jsAlert(($print_count .' ? '. $max_print));
      $msg = str_replace('{max_print}', $max_print, __('Selected items NOT ADDED to print queue. Only {max_print} can be printed at once'));
      utility::jsToastr('Item Barcode', $msg, 'warning');
    } else {
      utility::jsToastr('Item Barcode', __('Selected items added to print queue'), 'success');
    }
    exit();
}