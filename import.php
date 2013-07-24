<?php
/***
 * id / word / tf / idf / attr / rate
 *
 */
$fp = fopen();
$row = fgets($fp);
while($row = fgets($fp)){
	list($word, $tf, $idf, $attr) = explode("\t", $row);
	$word = addslashes($word);
	$tf = addslashes($tf);
	$idf = addslashes($idf);
	$attr = addslashes($attr);
	$check_sql = sprintf("SELECT COUNT(*) AS total FROM scws_dict WHERE word LIKE '%s'", $word);
	$result = $DB->query_first($check_sql);
	if($result['total'] == 0){
		$sql = sprintf("INSERT INTO scws_dict SET word = '%s', tf = '%s', idf = '%s', attr = '%s'", $word, $tf, $idf, $attr);
		$DB->query($sql);
	}
}
fclose($fp);
?>