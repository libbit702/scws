<?php
require_once 'common.php';
require_once 'class.curl.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'view';
if($action == 'submit'){
    $curl = new CURL();
    $result = $curl->post('http://www.xunsearch.com/scws/demo/v48.php', $_POST);    
    $result_pattern = "/888;\">([\s\S]*)<\/textarea/";
    preg_match($result_pattern, $result, $matches);
    $word_match = $matches[1];
    $words = explode(' ', $word_match);
    $scws_result = array();
    foreach($words as $word){
        $word = trim($word);
        $scws = array('word' => $word);
        $word_sql = sprintf("SELECT * FROM %s WHERE word = '%s'", "scws_dict", $word);
        $word_result = $DB->query_first($word_sql);
        if(!empty($word_result)){
            $scws['rating'] = $word_result['rating'];
            $scws['attr'] = $word_result['attr'];
        }
        $scws_result[] = $scws;
    }
}

if($action == 'view'){
    
}
?>
<!DOCTYPE html>
<html>
  <head>
  	<meta charset="utf-8">
    <title>Bootstrap 101 Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
  </head>
  <body>
  	<ul class="nav nav-tabs">
	  <li><a href="index.php">管理词典</a></li>
	  <li class="active"><a href="scws_test.php">指数测试</a></li>
	</ul>
    <form class="form-horizontal" action="scws_test.php?action=submit" method="post">
      <div class="control-group">
        <textarea rows="3" name="mydata"></textarea>
      </div>
      <div class="control-group">
        <div class="controls">
          <button type="submit" class="btn">提交分词</button>
        </div>
      </div>
    </form>
    <?php if(!empty($scws_result)): ?>
    <table class="table span6">
      <thead>
        <tr>
          <th>WORD</th>.
          <th>ATTR</th>
          <th>RATING</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($scws_result as $r): ?>
        <tr>
          <td><?php echo $r['word']; ?></td>
          <td><?php echo $r['attr']; ?></td>
          <td><?php echo $r['rating']; ?></td>
        </tr>
        <tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script>
    (function($){
    	
    })(jQuery)
    </script>
  </body>
</html>