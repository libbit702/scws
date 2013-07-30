<?php
error_reporting(7);



function getmicrotime() {
        global $script_start_time;
        $script_start_time = microtime();
        list($usec, $sec) = explode(" ", $script_start_time);
        return ((float)$usec + (float)$sec);
} 

$time_start = getmicrotime();

$config_file = "../../../inc/config/config.php";

require_once $config_file;


$real_path = realpath("./");
// 设置 include_path 为绝对路径
ini_set('include_path', INCLUDE_PATH . "/library" . PATH_SEPARATOR . ini_get('include_path'));
ini_set('include_path', INCLUDE_PATH . "/config" . PATH_SEPARATOR . ini_get('include_path'));


//require_once "class.pager.php";
require_once "class.smarttemplate.php";

require_once "class.smartconfig.php";

$ini  =  new SmartConfig();
$_SETTINGS = $ini->read(INI_FILE);


require_once "class.smartcache.php";

//require_once "db_mysql.php";
if ($_GET['debug'] == 1 AND $_GET['debugKey'] == 'jeboo328') {
	require_once "db_mysql_debug.php";
} else {
	require_once "db_mysql.php";
}

$DB = new db_MySQL;
$DB->database = "action";
$DB->server = $server;
$DB->user = $datauser;
$DB->password = $datapassword;

$DB->connect();
$DB->use_cache = false; 

require_once "functions.php";

require_once "class.xluser.php" ;

$XLuser = new XLuser( "221.238.252.125" , 39527 ) ;

?>