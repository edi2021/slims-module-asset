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