<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-12 20:21:13
 * @modify date 2021-08-12 20:21:13
 * @desc [description]
 */

function mutationZero($DB, $Data)
{
    return $Data[0] . '&handler=Record&method=EditData&view=editAsset';
}

function mutationZeroItem($DB, $Data)
{
    return $Data[0] . '&handler=Record&method=EditData&view=editItemAsset';
}

function mutationMstAuthorization($DB, $Data)
{
    return $Data[0] . '&handler=Master&method=EditDataMaster&view=masterAuthorization';
}

function mutationMstSource($DB, $Data)
{
    return $Data[0] . '&handler=Master&method=EditDataMaster&view=masterSource';
}

function mutationMstCondition($DB, $Data)
{
    return $Data[0] . '&handler=Master&method=EditDataMaster&view=masterCondition';
}

function mutationMstStatus($DB, $Data)
{
    return $Data[0] . '&handler=Master&method=EditDataMaster&view=masterStatus';
}


function mutationSetItemLabel($DB, $Data)
{
    return '<b>' .$Data[2]. '</b>';
}

function postCondition($keys)
{
    $_POST = (empty($_POST)) ? json_decode(file_get_contents('php://input'), TRUE) : $_POST;

    if (isset($_POST['tableName']))
    {
        $getHandler = explode('::', trim($_POST['tableName']));
        $_POST[$keys] = $getHandler[0];
        $_POST['tableName'] = $getHandler[1];
    }

    if (isset($_POST['itemAction']))
    {
        $_POST[$keys] = 'Record';
        $_POST['method'] = 'delete' . $_POST['form_name'];
    }
}

function mutationCountItem($DB, $Data)
{
    $id = $DB->escape_string(substr($Data[0], 0,1));
    $q = $DB->query('select count(itemcode) from asset_item where assetid = ' . $id);

    return $q->fetch_row()[0];
}