<?php

/**
 * Make Sure the DB Dump has:
 *     DROP TABLE IF EXISTS `attempts`;
 *     CREATE TABLE `attempts` (
 * otherwise, it may fall in error
 * For Windows, mysql.exe full path may require
 */

//run backup from cPanel Cron job:
// mysql -h localhost -u [myDbUser] -p[MyPassword] [myDB] < /path/db_data.sql >/dev/null 2>&1

$host = "localhost";
$file = __DIR__ . '/backup/my_dump_data.sql';
$user = "user";
$pass = 'password'; /// empty password will cause issue.
$db = "my_dump_data";

/*
 *
 */
//$cmd = "mysql -h {$host} -u {$user} -p{$pass} {$db} < $file";
//echo exec($cmd);
//echo "DONE";
//echo '<br />';
//echo $cmd;


$dbMan = new MysqlBackupRestore($user,$pass,$host);
$dbMan->backup($db);
$dbMan->restore($db,$file);

/**
 * php class to backup and restore mysql database
 *
 * LICENSE: GNU GENERAL PUBLIC LICENSE
 * Copyright (C) 2007 Free Software Foundation, Inc. <http://fsf.org/>
 * Everyone is permitted to copy and distribute verbatim copies
 * of this license document, but changing it is not allowed.
 *
 * @category   Class
 * @package    MakeWebSmart\MysqlBackupRestore
 * @author     Azraf <c.azraf@gmail.com>
 * @copyright  1997-2005 The PHP Group
 * @version    1.0
 */
/**
 * This is a "Docblock Comment," also known as a "docblock."  The class'
 * docblock, below, contains a complete description of how to write these.
 */

class MysqlBackupRestore{
    private $_user;
    private $_pass;
    private $_path;
    private $_host = null;
    private $_port = null;

    public function __construct($user,$pass,$host='localhost',$port=false)
    {
        $this->_user = $user;
        $this->_pass = $pass;
        $this->_host = $host;
        $this->_path = __DIR__ . '/backup/';
        if($port){
            $this->_port = $port;
        }
    }

    public function backup($db,$path=false,$addTime=false)
    {
        if(!$path){
            $path = $this->_path;
        }
        if(is_array($db)){
            foreach($db as $v)
            {
                $this->_doBackup($v,$path,$addTime);
            }
        }else {
            $this->_doBackup($db,$path,$addTime);
        }
    }

    private function _doBackup($db,$path,$timer=false)
    {
        $time = ($timer) ? date("Y-m-d-H") : '';
        $cmd = "mysqldump --routines -h {$this->_host} -u {$this->_user} -p{$this->_pass} {$db} > " . $path . "{$db}_{$time}.sql";
        exec($cmd);
    }

    public function restore($db,$file)
    {
        if(is_array($db)){
            foreach($db as $v)
            {
                $this->_doRestore($v,$file);
            }
        }else {
            $this->_doRestore($db,$file);
        }
    }

    private function _doRestore($db,$file)
    {
        $cmd = "mysql -h {$this->_host} -u {$this->_user} -p{$this->_pass} {$db} < $file";
        exec($cmd);
    }
}
?>