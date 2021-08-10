<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-10 19:32:40
 * @modify date 2021-08-10 19:32:40
 * @desc [description]
 */

namespace SLiMSAssetmanager\Handler;

use SLiMS\DB;

class Ajax
{
    private function search()
    {
        $dbs = DB::getInstance('mysqli');

        // Took from AJAX_lookup_handler.php 
        if(empty($_POST)) $_POST = json_decode(file_get_contents('php://input'), true);

        foreach ($_POST as $key => $value) {
            $_POST[$key] = $dbs->escape_string($value);
        }

        // list limit
        $limit = 20;

        $table_name = trim($_POST['tableName']);
        $table_fields = trim($_POST['tableFields']);

        if (isset($_POST['keywords']) AND !empty($_POST['keywords'])) {
            $keywords = urldecode(ltrim($_POST['keywords']));
        } else {
            $keywords = '';
        }

        // explode table fields data
        $fields = str_replace(':', ', ', $table_fields);
        // set where criteria
        $criteria = '';
        foreach (explode(':', $table_fields) as $field) {
            $criteria .= " $field LIKE '%$keywords%' OR";
        }
        // remove the last OR
        $criteria = substr_replace($criteria, '', -2);

        $sql_string = "SELECT $fields ";

        // append table name
        $sql_string .= " FROM $table_name ";
        if ($criteria) { $sql_string .= " WHERE $criteria LIMIT $limit"; }

        // send query to database
        $query = $dbs->query($sql_string);
        $error = $dbs->error;
        $data = array();

        if (isset($_GET['format'])) {
            if ($_GET['format'] == 'json') {
                header('Contenty-Type: application/json');
          
                if ($error) { echo json_encode(array('id' => 0, 'text' => $error)); }
                if ($query->num_rows > 0) {
                  while ($row = $query->fetch_row()) {
                    $data[] = array('id' => $row[0], 'text' => $row[1].(isset($row[2])?' - '.$row[2]:'').(isset($row[3])?' - '.$row[3]:''));
                  }
                } else {
                    if (isset($_GET['allowNew'])) {
                        $data[] = array('id' => 'NEW:'.$keywords, 'text' => $keywords.' &lt;'.__('Add New').'&gt;');
                      } else {
                      $data[] = array('id' => 'NONE', 'text' => 'NO DATA FOUND');
                      }
                }
                echo json_encode($data);
            }
            exit();
        }
    }

    public function run()
    {
        $this->search();
    }
}