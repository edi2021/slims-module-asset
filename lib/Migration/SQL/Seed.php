<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-15 14:29:18
 * @modify date 2021-08-15 14:29:18
 * @desc [description]
 */

namespace SLiMSAssetmanager\migration\SQL;

trait Seed
{
    public function plantIt()
    {
        $SQL = <<<SQL
            CREATE TABLE IF NOT EXISTS `asset` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` text COLLATE ucs2_unicode_ci DEFAULT NULL,
            `typeid` int(11) NOT NULL DEFAULT 0,
            `markid` int(11) NOT NULL DEFAULT 0,
            `authorizationid` int(11) NOT NULL DEFAULT 0,
            `image` text COLLATE ucs2_unicode_ci DEFAULT NULL,
            `notes` text COLLATE ucs2_unicode_ci DEFAULT NULL,
            `inputdate` datetime DEFAULT NULL,
            `lastupdate` datetime DEFAULT NULL,
            `uid` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            KEY `nameasset_notes` (`name`(50),`notes`(50)),
            KEY `typeid` (`typeid`),
            KEY `markid` (`markid`),
            KEY `authorizationid` (`authorizationid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=ucs2 COLLATE=ucs2_unicode_ci;

            CREATE TABLE IF NOT EXISTS `asset_agent` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(75) COLLATE utf8mb4_bin DEFAULT NULL,
            `detail` text COLLATE utf8mb4_bin DEFAULT NULL,
            `inputdate` datetime DEFAULT NULL,
            `lastupdate` datetime DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

            CREATE TABLE IF NOT EXISTS `asset_authorization` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
            `detail` text COLLATE utf8mb4_bin DEFAULT NULL,
            `inputdate` datetime DEFAULT NULL,
            `lastupdate` datetime DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

            CREATE TABLE IF NOT EXISTS `asset_condition` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(75) COLLATE utf8mb4_bin DEFAULT NULL,
            `detail` text COLLATE utf8mb4_bin DEFAULT NULL,
            `inputdate` datetime DEFAULT NULL,
            `lastupdate` datetime DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


            CREATE TABLE IF NOT EXISTS `asset_file` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
            `path` text COLLATE utf8mb4_bin DEFAULT NULL,
            `description` text COLLATE utf8mb4_bin DEFAULT NULL,
            `inputdate` datetime DEFAULT NULL,
            `lastupdate` datetime DEFAULT NULL,
            `uid` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

            CREATE TABLE IF NOT EXISTS `asset_item` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `assetid` int(11) NOT NULL DEFAULT 0,
            `itemcode` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
            `locationid` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL,
            `placedetail` text COLLATE utf8mb4_bin DEFAULT NULL,
            `source` int(11) NOT NULL DEFAULT 0,
            `orderdate` date DEFAULT NULL,
            `receivedate` date DEFAULT NULL,
            `idorder` text COLLATE utf8mb4_bin DEFAULT NULL,
            `invoice` text COLLATE utf8mb4_bin DEFAULT NULL,
            `invoicedate` date DEFAULT NULL,
            `agentid` int(11) NOT NULL DEFAULT 0,
            `conditionid` int(11) NOT NULL DEFAULT 0,
            `price` int(11) NOT NULL DEFAULT 0,
            `pricecurrency` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL,
            `deleted` int(11) NOT NULL DEFAULT 1,
            `inputdate` datetime DEFAULT NULL,
            `lastupdate` datetime DEFAULT NULL,
            `deleteddate` datetime DEFAULT NULL,
            `uid` int(11) DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `itemcode` (`itemcode`) USING HASH,
            KEY `assetid` (`assetid`),
            KEY `locationid_agentid` (`locationid`,`agentid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

            CREATE TABLE IF NOT EXISTS `asset_mark` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(75) COLLATE utf8mb4_bin DEFAULT NULL,
            `detail` text COLLATE utf8mb4_bin DEFAULT NULL,
            `inputdate` datetime DEFAULT NULL,
            `lastupdate` datetime DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


            CREATE TABLE IF NOT EXISTS `asset_meta_file` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `assetid` int(11) NOT NULL DEFAULT 0,
            `fileid` int(11) NOT NULL DEFAULT 0,
            `inputdate` datetime DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `assetid_fileid` (`assetid`,`fileid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


            CREATE TABLE IF NOT EXISTS `asset_source` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(75) COLLATE utf8mb4_bin DEFAULT NULL,
            `detail` text COLLATE utf8mb4_bin DEFAULT NULL,
            `inputdate` datetime DEFAULT NULL,
            `lastupdate` datetime DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


            CREATE TABLE IF NOT EXISTS `asset_status` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(75) COLLATE utf8mb4_bin DEFAULT NULL,
            `detail` text COLLATE utf8mb4_bin DEFAULT NULL,
            `inputdate` datetime DEFAULT NULL,
            `lastupdate` datetime DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


            CREATE TABLE IF NOT EXISTS `asset_type` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
            `inputdate` datetime DEFAULT NULL,
            `lastupdate` datetime DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
            
        SQL;

        return $SQL;
    }

    public function makeHistory(object $Instance, $Seedname, $Version)
    {        
        @$Instance->query("CREATE TABLE IF NOT EXISTS `asset_migration` (
            `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `seedname` varchar(50) NULL,
            `version` varchar(50) NULL,
            `inputdate` datetime NULL ON UPDATE CURRENT_TIMESTAMP
          ) ENGINE='MyISAM';");

        @$Instance
            ->prepare("INSERT INTO asset_migration SET seedname = :seedname, version = :version, inputdate = :inputdate")
            ->execute(['seedname' => $Seedname, 'version' => $Version, 'inputdate' => date('Y-m-d H:i:s')]);
    }
}
