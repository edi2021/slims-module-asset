<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-10 18:48:33
 * @modify date 2021-08-10 18:48:33
 * @desc [description]
 */

namespace SLiMSAssetmanager\View;

// Load dependency
use \simbio_form_table_AJAX as Form;
use \simbio_form_element as FE;
use SLiMS\DB;
use SLiMSAssetmanager\Ui\Box;

class addAsset
{
    // set box
    protected static function box()
    {
        // Set Box
        $Box = new Box($_SERVER['PHP_SELF'], 'GET');

        $Box
            ->setTitle('Asset Perpustakaan')
            ->setActionButton([
                    ['url' => $_SERVER['PHP_SELF'] . httpQuery(['handler' => 'Record', 'method' => 'addForm', 'view' => 'addAsset']), 'label' => 'Tambah Data', 'class' => 'btn btn-primary'],
                    ['url' => $_SERVER['PHP_SELF'], 'label' => 'Daftar Asset', 'class' => 'btn btn-default']
                ])
            ->make();
    }

    protected static function getOption(string $table)
    {
        $DB = DB::getInstance();
        $Table = str_replace(['\'', '"', '`'], '', $table);
        $Data = $DB->query('select id,name from '.$Table);

        // Options
        $Options = [];
        $Options[] = ['0', 'Pilih'];
        while($result = $Data->fetch(\PDO::FETCH_NUM))
        {
            $Options[] = [$result[0], $result[1]];
        }

        return $Options;
    }

    protected static function getRawOptions(string $label, string $column, string $table)
    {
        $dbs = DB::getInstance('mysqli');
        $Table = str_replace(['\'', '"', '`'], '', $table);
        $Column = str_replace(['\'', '"', '`'], '', $column);

        $q = $dbs->query("SELECT $Column FROM $Table");
        $options = [['', '-- ' . __($label) . ' --']];

        while ($d = $q->fetch_row()) {
            $options[] = [$d[0], $d[1]];
        }

        return $options;
    }

    private static function getPattern()
    {
        $dbs = DB::getInstance();
        $Pattern = $dbs->query('select setting_value from `setting` where setting_name = "assetPattern"');

        $result = [];
        if ($Pattern->rowCount())
        {
            $data = json_decode($Pattern->fetch(\PDO::FETCH_NUM)[0], TRUE);
            
            foreach ($data as $pattern) {
                $result[] = [$pattern['label'],$pattern['label']];
            }
        }

        return $result;
    }

    protected static function setItems()
    {
        global $sysconf;
        
        $popPattern = $_SERVER['PHP_SELF'] . '?handler=Modal&method=popUp&view=popPattern';

        $formElement = [
            'codeList' => FE::selectList('itemCodePattern', self::getPattern(), '', 'class="form-control col"'),
            'locationList' => FE::selectList('locationID', self::getRawOptions(__('Location'), 'location_id, location_name', 'mst_location'), '', 'class="form-control col-4"') . '</div>',
            'itemSite' => FE::textField('text', 'itemSite', '', 'class="form-control col-4"') . '</div>',
            'collType' => FE::selectList('collTypeID', self::getRawOptions(__('Collection Type'), 'coll_type_id, coll_type_name', 'mst_coll_type'), '', 'class="form-control col-4"') . '</div> ',
            'itemStatus' => FE::selectList('itemStatusID', self::getRawOptions(__('Item Status'), 'item_status_id, item_status_name', 'mst_item_status'), '', 'class="form-control col-4"') . '</div> ',
            'source' => FE::selectList('source', [['1', __('Buy')], ['2', __('Prize/Grant')]], '', 'class="form-control col-4"') . '</div> ',
            'orderDate' => FE::dateField('orDate', date('Y-m-d'), 'class="form-control"') . '</div>',
            'recvDate' => FE::dateField('recvDate', date('Y-m-d'), ' class="form-control col-12"') . '</div>',
            'ordNo' => FE::textField('text', 'ordNo', '', 'class="form-control"') . '</div>',
            'invoice' => FE::textField('text', 'invoice', '', 'class="form-control col-4"') . '</div>',
            'invoceDate' => FE::dateField('invcDate', date('Y-m-d'), ' class="form-control col-12"') . '</div>',
            'supplier' => FE::selectList('supplierID', self::getRawOptions(__('Supplier'), 'supplier_id, supplier_name', 'mst_supplier'), '', 'class="form-control col-4"') . '</div> ',
            'price' => FE::textField('text', 'price', '0', 'class="form-control col-3"') . FE::selectList('priceCurrency', $sysconf['currencies'], '', 'class="form-control col-2"') . '</div> ',
            'visibility' => 'makeVisible s-margin__bottom-1',
        ];

        extract($formElement);

        // Translate
        $translate = [
            'totalItemsTrans' => __('Total item(s)'),
            'optionsTrans' => __('Options'),
            'addNewPatternTrans' => __('Add New Pattern'),
            'locationTrans' => __('Location'),
            'shelLocationTrans' => 'Lokasi Penempatan',
            'ItemStatusTrans' => 'Status Barang',
            'SourceTrans' => __('Source'),
            'OrderDateTrans' => __('Order Date'),
            'ReceivingDateTrans' => __('Receiving Date'),
            'OrderNumberTrans' => __('Order Number'),
            'InvoiceTrans' => __('Invoice'),
            'InvoiceDateTrans' => __('Invoice Date'),
            'SupplierTrans' => __('Supplier'),
            'PriceTrans' => __('Price')
        ];

        extract($translate);

        $item = <<<HTML
            <div class="form-inline">
                {$codeList}&nbsp;
                <input type="text" class="form-control col-4" name="totalItems" placeholder="{$totalItemsTrans}" />&nbsp;
                <div class="{$visibility}"><div class="bnt btn-group"><div class="btn btn-info" data-toggle="collapse" data-target="#batchItemDetail" aria-expanded="false" aria-controls="batchItemDetail">{$optionsTrans}</div>
                <a href="{$popPattern}" height="420px" class="s-btn btn btn-default notAJAX openPopUp notIframe"  title="{$addNewPatternTrans}">{$addNewPatternTrans}</a></div></div>
                <div class="collapse" id="batchItemDetail" style="padding:10px;width:100%; text-align:left !important;">
                <div class="form-group divRow p-1"><div class="col-3">{$locationTrans}'</div>
                {$locationList}
                <div class="form-group divRow p-1"><div class="col-3">{$shelLocationTrans}</div>
                {$itemSite}
                <div class="form-group divRow p-1"><div class="col-3">{$ItemStatusTrans}</div>
                {$itemStatus}
                <div class="form-group divRow p-1"><div class="col-3">{$SourceTrans}</div>
                {$source}
                <div class="form-group divRow p-1"><div class="col-3">{$OrderDateTrans}</div>
                {$orderDate}
                <div class="form-group divRow p-1"><div class="col-3">{$ReceivingDateTrans}</div>
                {$recvDate}
                <div class="form-group divRow p-1"><div class="col-3">{$OrderNumberTrans}</div>
                {$ordNo}
                <div class="form-group divRow p-1"><div class="col-3">{$InvoiceTrans}</div>
                {$invoice}                
                <div class="form-group divRow p-1"><div class="col-3">{$InvoiceDateTrans}</div>
                {$invoceDate}
                <div class="form-group divRow p-1"><div class="col-3">{$SupplierTrans}</div>
                {$supplier}
                <div class="form-group divRow p-1"><div class="col-3">{$PriceTrans}</div>
                {$price}
            </div>
        HTML;

        return $item;
    }

    protected static function attachment()
    {
        $visibility = 'makeVisible s-margin__bottom-1';
        $href = $_SERVER['PHP_SELF'] . '?handler=Modal&method=popUp&view=popAttach';
        $iframeSrc = $_SERVER['PHP_SELF'] . '?handler=Iframe&method=list&view=listAttach';
        $label = __('Add Attachment');
        $titleLabel = __('File Attachments');

        $HTML = <<<HTML
            <div class="{$visibility}"><a class="s-btn btn btn-default notAJAX openPopUp" href="{$href}" width="780" height="500" title="{$titleLabel}">{$label}</a></div>';
            <iframe name="attachIframe" id="attachIframe" class="form-control" style="width: 100%; height: 100px;" src="{$iframeSrc}"></iframe>';
        HTML;

        return $HTML;
    }

    protected static function image()
    {
        return FE::textField('file', 'image', '');
    }

    // set render
    public static function render()
    {
        // set Form instance
        $Form = new Form('addAssetForm', $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'], 'post');

        // set prop
        $property = [
            'submit_button_attr' => 'name="saveData" value="' . __('Save') . '" class="s-btn btn btn-default"',
            'table_attr' => 'id="dataList" cellpadding="0" cellspacing="0"',
            'table_header_attr' => 'class="alterCell"',
            'table_content_attr' => 'class="alterCell2"'
        ];

        // set fields
        $fields = [
            ['addTextField' => ['textarea', 'name', 'Nama Barang' . '*', '', 'rows="1" class="form-control"', 'Isikan nama barang yang hendak di record']],
            ['addSelectList' => ['typeid', 'Jenis Barang', self::getOption('asset_type'), '', 'class="select2" data-src="' .$_SERVER['PHP_SELF'] . '?format=json&allowNew=true" data-src-table="Ajax::asset_type" data-src-cols="id:name"', 'Jenis barang']],
            ['addSelectList' => ['markid', 'Merek', self::getOption('asset_mark'), '', 'class="select2" data-src="' .$_SERVER['PHP_SELF'] . '?format=json&allowNew=true" data-src-table="Ajax::asset_mark" data-src-cols="id:name"', 'Jenis barang']],
            ['addAnything' => ['Nomor QR Code', self::setItems()]],
            ['addSelectList' => ['authorizationid', 'Penguasaan', self::getOption('asset_type'), '', 'class="select form-control" style="width: 25%"', 'Jenis barang']],
            ['addAnything' => ['Dokumen Terkait', self::attachment()]],
            ['addAnything' => ['Foto', self::image()]],
            ['addTextField' => ['textarea', 'notes', 'Keterangan' . '*', '', 'rows="1" style="height: 100px;" class="form-control"', 'Isikan keterangan yang hendak di record']]
        ];

        // register property
        foreach ($property as $prop => $value) {
            $Form->{$prop} = $value;
        }

        // Register field
        foreach ($fields as $field) {
            $method = array_key_first($field);
            $parameter = array_values($field)[0];
            call_user_func_array([$Form, $method], $parameter);
        }

        // Set Box
        self::box();

        // make form
        echo $Form->printOut();
    }
}