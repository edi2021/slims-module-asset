<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-15 08:13:47
 * @modify date 2021-08-15 08:13:47
 * @desc [description]
 */

namespace SLiMSAssetmanager\View;

use \simbio_table as ReportTable;
use SLiMSAssetmanager\Ui\{Box,Html};
use SLiMSAssetmanager\View\popPattern;
use SLiMS\DB;

class codePatternList extends popPattern
{
    private static function box()
    {
        // Set Box
        $Box = new Box($_SERVER['PHP_SELF'] . '?view=codePatternList', 'GET');

        $Box
            ->setTitle('Nomor pola asset')
            ->setActionButton([
                ['url' => $_SERVER['PHP_SELF'] . '?view=codePatternList&addForm=true', 'label' => 'Tambah Data', 'class' => 'btn btn-primary'],
                ['url' => $_SERVER['PHP_SELF'] . '?view=codePatternList', 'label' => 'Daftar Asset', 'class' => 'btn btn-default']
            ])
            ->make();
    }

    private static function getData()
    {
        $Data = [];
        $DB = DB::getInstance();

        if (isset($_GET['id']) && !empty($_GET['id']))
        {
            // get pattern
            $Pattern = $DB->query('select setting_value from setting where setting_name = \'assetPattern\'');
            $PatternDatas = json_decode($Pattern->fetch(\PDO::FETCH_NUM)[0], true);
            $Data = array_values(array_filter($PatternDatas, function($data){
                if ($_GET['id'] === $data['label'])
                {
                    return true;
                }
            }))[0];
        }

        return $Data;
    }

    public static function render($Data = [])
    {
        if (isset($_GET['addForm']))
        {
            parent::render(self::getData());
            exit;
        }

        $DB = DB::getInstance();

        $Report = new ReportTable();
        $Report->table_attr = 'class="s-table table table-bordered mb-0"';
        $Report->setHeader(['Pola', 'Keterangan', 'Aksi']);
        $Report->table_header_attr = 'class="dataListHeader"';
        $Report->setCellAttr('style="max-width: 10px; width: auto"', 0, 'colspan="2"');

        // get pattern
        $Pattern = $DB->query('select setting_value from setting where setting_name = \'assetPattern\'');

        $Result = [];
        if ($Pattern->rowCount())
        {
            $PatternDatas = json_decode($Pattern->fetch(\PDO::FETCH_NUM)[0], true);

            if (isset($_GET['keywords']))
            {
                $PatternDatas = array_values(array_filter($PatternDatas, function($data){
                    if (preg_match('/' . $_GET['keywords'] . '/i', $data['label']))
                    {
                        return true;
                    }
                }));
            }

            foreach ($PatternDatas as $PatternData) {
                $Result['<b>' . $PatternData['label'] . '</b>'] = function() use($PatternData) {
                    Html::$writeMode = 'return';
                    return 
                            Html::write('b', 'Prefix') . ':' . $PatternData['data'][0] . '&nbsp;' .
                            Html::write('b', 'Suffix') . ':' . $PatternData['data'][1] . '&nbsp;' . 
                            Html::write('b', 'Panjang Nomor') . ':' . $PatternData['data'][2] . '&nbsp;';
                };
            }
        }

        self::box();
        $row = 1;
        foreach ($Result as $Label => $value) {
            $id = strip_tags($Label);
            Html::$writeMode = 'return';
            $Edit = Html::write('a', '', ['class' => 'editLink', 'href' => AWB . 'modules/asset/index.php?view=codePatternList&addForm=true&id='.$id, 'title' => 'Edit']);

            $Report->appendTableRow([$Label, (is_callable($value)) ? $value() : $value, $Edit]);
            // set cell attribute
            $Report->setCellAttr($row, 0, 'class="alterCell" valign="top" style="max-width: 10px; width: auto"');
            $Report->setCellAttr($row, 1, 'class="alterCell" valign="top" style="width: auto;"');
            $Report->setCellAttr($row, 2, 'class="alterCell" valign="top" style="width: auto;"');
            // add row count
            $row++;
        }
        echo $Report->printTable();
    }
}