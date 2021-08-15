<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-14 18:17:10
 * @modify date 2021-08-14 18:17:10
 * @desc [description]
 */

namespace SLiMSAssetmanager\View;

// dependency
use \simbio_table as ReportTable;
use SLiMSAssetmanager\Ui\Box;
use SLiMS\DB;

class report extends ReportTable
{
    private $db;
    private function __construct()
    {
        parent::__construct();
        $this->db = DB::getInstance();
    }

    private function box()
    {
        // Set Box
        $Box = new Box($_SERVER['PHP_SELF'], 'GET');
        $Box->disableForm = true;

        $Box
            ->setTitle('Statistik Asset Perpustakaan')
            ->make();
    }

    public static function render()
    {
        $Report = new report();
        $Report->table_attr = 'class="s-table table table-bordered mb-0"';
        $Report->setHeader([__('Collection Statistic Summary')]);
        $Report->table_header_attr = 'class="dataListHeader"';
        $Report->setCellAttr(0, 0, 'colspan="2"');

        $Fields = [
            'Jumlah Barang' => function() use($Report) {
                return $Report->db->query('select count(id) from asset')->fetch(\PDO::FETCH_NUM)[0];
            },
            'Jumlah Item Aset' => function() use($Report) {
                return $Report->db->query('select count(id) from asset_item')->fetch(\PDO::FETCH_NUM)[0];
            },
            'Jumlah Item Aset Yang Dihapus' => function() use($Report) {
                return $Report->db->query('select count(id) from asset_item where deleted = 1')->fetch(\PDO::FETCH_NUM)[0];
            },
            'Jumlah Jenis Barang' => function() use($Report) {
                return $Report->db->query('select count(id) from asset_type')->fetch(\PDO::FETCH_NUM)[0];
            }
        ];
        
        // initial row count
        $row = 1;
        foreach ($Fields as $Label => $value) {
            $Report->appendTableRow([$Label, (is_callable($value)) ? $value() : $value]);
            // set cell attribute
            $Report->setCellAttr($row, 0, 'class="alterCell" valign="top" style="width: 300px;"');
            $Report->setCellAttr($row, 1, 'class="alterCell" valign="top" style="width: auto;"');
            // add row count
            $row++;
        }

        $Report->box();
        echo $Report->printTable();
    }
}