<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-09 23:48:33
 * @modify date 2021-08-09 23:48:33
 * @desc [description]
 */

function dd($mix, $exit = true)
{
  echo '<pre>';
  var_dump($mix);
  echo '</pre>';
  
  if ($exit) exit;
}

function httpQuery(array $data = [])
{
  return '?' . http_build_query(array_unique(array_merge($_GET, $data)));
}

function isClassExists(string $className)
{
   return class_exists('SLiMSAssetmanager\\' . $className);
}

function getOptionsId($dbs, $table, $criteria)
{
  $table = $dbs->escape_string($table);
  $criteria = $dbs->escape_string($criteria);
  
  if (is_numeric($criteria))
  {
    return $criteria;
  }

  // run Query
  $q = $dbs->query("select * from $table where name = '$criteria'");

  // check
  if ($q->num_rows === 0)
  {
    $date = date('Y-m-d H:i:s');
    @$dbs->query("insert into $table set name = '$criteria', inputdate = '$date', lastupdate = '$date'");
    return $dbs->insert_id;
  }

  // fetch data
  $d = $q->fetch_assoc();
  return $d['id'];
}

function countPattern($dbs, $Pattern)
{
  // set Regexp attribute
  $Prefix = $Pattern['data'][0];
  $Suffix = $Pattern['data'][1];
  // set query
  $q = $dbs->query("select count(itemcode) from asset_item where itemcode regexp '^$Prefix+[0-9]+$Suffix'");

  return $q->fetch_row()[0];
}

function simbioRedirect(string $to, string $selector = '#mainContent')
{
  $HTML = <<<HTML
    <script>
      parent.$('{$selector}').simbioAJAX('{$to}')
    </script>
  HTML;

  echo $HTML;
}

function setData($Key, $Data, $Callback = '')
{
  if (isset($Data[$Key]))
  {
     if (is_callable($Callback))
     {
       return $Callback($Data);
     }

     return $Data[$Key];
  }
}

function tableCheck()
{
  $DB = SLiMS\DB::getInstance();

  $Check = $DB->query("show tables like 'asset_%'");

  if ($Check->rowCount() < 10)
  {
    $Seed = __DIR__ . DS . 'lib' . DS .'Migration' . DS . 'Seed';
    $Engine = SLiMSAssetmanager\Migration\Engine::ignite($Seed);

    $Engine->pullClutch()->openThrottle();

    simbioRedirect($_SERVER['PHP_SELF']);
    exit;
  }
}