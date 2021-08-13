<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-10 12:15:58
 * @modify date 2021-08-12 09:43:55
 * @desc [description]
 */

namespace SLiMSAssetmanager\Ui;

class Grid extends \simbio_datagrid
{
    private $db;
    private $props = [];
    private $tableSpec;
    private $numToShow;
    private $criteria = [];
    private $result;
    public $canEdit = true;

    public function __construct(object $db, array $props, int $numToShow = 20)
    {
        // set parent contruct
        parent::__construct();
        // set prop
        $this->props = $props;
        // set database object
        $this->db = $db;
        // set limit number to show data
        $this->numToShow = $numToShow;
    }

    public function setTableSpec(string $tableSpec)
    {
        $this->tableSpec = $tableSpec;
        return $this;
    }

    public function setColumn()
    {
        call_user_func_array([$this, 'setSQLColumn'], func_get_args());
        return $this;
    }

    public function setJoin(string $table, string $relatedColumn, string $type = '')
    {
        $this->tableSpec = $this->tableSpec . ' ' . trim($type) . ' join ' . $table . ' on ' . $relatedColumn;
        return $this;
    }

    public function setCriteria(string $columnToMatach, bool $raw = false)
    {
        if (isset($_GET['keywords']) AND $_GET['keywords']) 
        {
            $keywords = \utility::filterData('keywords', 'get', true, true, true);
            if (!$raw)
            {
                $this->criteria[] = ' ' . $columnToMatach . ' = "' . $keywords . '" ';
            }
            else
            {
                $this->criteria[] = str_replace('{keyword}', $keywords, $columnToMatach);
            }
        }

        return $this;
    }

    public function getSearchInfo()
    {
        if (isset($_GET['keywords']) AND $_GET['keywords']) {
            $msg = str_replace('{result->num_rows}', $this->num_rows, __('Found <strong>{result->num_rows}</strong> from your keywords'));
            echo '<div class="infoBox">' . $msg . ' : "' . htmlspecialchars($_GET['keywords']) . '"<div>' . __('Query took') . ' <b>' . $this->query_time . '</b> ' . __('second(s) to complete') . '</div></div>';
        }
    }

    public function mutation(int $column, string $callback)
    {
        $this->modifyColumnContent($column, $callback);
        return $this;
    }

    public function make()
    {
        // set prop
        foreach ($this->props as $prop => $value) {
            $this->{$prop} =  $value;
        }

        // set criteria
        if (count($this->criteria))
        {
            $this->setSQLCriteria($this->sql_criteria . implode(' and ', $this->criteria));
        }

        $this->result = $this->createDataGrid($this->db, $this->tableSpec, $this->numToShow, $this->canEdit);
    }

    public function getResult()
    {
        echo $this->result;
    }
}