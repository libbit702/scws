<?php
/***
 * id / word / tf / idf / attr / rate
 *
 */
require_once('common.php');
ini_set('display_errors', TRUE);
error_reporting(E_ALL);
$fp = fopen('dict.utf8_dump.txt', 'r');
$row = fgets($fp);
$rowcnt = 1;
while($row = fgets($fp)){
    $row = trim($row);
	list($word, $tf, $idf, $attr) = explode("\t", $row);
	$word = addslashes($word);
	$tf = addslashes($tf);
	$idf = addslashes($idf);
	$attr = addslashes($attr);
	$check_sql = sprintf("SELECT COUNT(*) AS total FROM scws_dict WHERE word = '%s'", $word);
	$result = $DB->query_first($check_sql);
	if($result['total'] == 0){
		$sql = sprintf("INSERT INTO scws_dict SET word = '%s', tf = '%s', idf = '%s', attr = '%s'", $word, $tf, $idf, $attr);
		$DB->query($sql);
        //echo $sql;die();
	}
    $rowcnt++;
    echo $rowcnt . "\n";
}
fclose($fp);
?>