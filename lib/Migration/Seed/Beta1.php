<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-15 14:21:57
 * @modify date 2021-08-15 14:21:57
 * @desc [description]
 */

namespace SLiMSAssetmanager\migration\Seed;

use SLiMS\DB;
use SLiMSAssetmanager\migration\SQL\Seed;

class Beta1
{
    use Seed;

    public function up()
    {   
        $DB = DB::getInstance();

        // set up table
        $DB->query($this->plantIt());

        // Make History
        $this->makeHistory($DB, 'Beta1', 1);
    }

    public function down()
    {

    }
}