<?php
require_once 'common.php';
require_once dirname(__FILE__) . "/class.pager.php";
require_once 'class.curl.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'view';

if($action == 'view'){
    $where = array();
    if(isset($_GET['attr'])){
        $where[] = ' attr = "' . addslashes($_GET['attr']) . '"';
    }
    
    if(isset($_GET['word'])){
        $where[] = ' word = "' . addslashes($_GET['word']) . '"';
    }

    $where_str = " WHERE 1 ";
    if(!empty($where)){
        $where_str .= 'AND ' . implode(' AND ', $where);
    }

    $total_sql = sprintf("SELECT COUNT(*) AS count FROM %s %s", 'scws_dict', $where_str);
    $total = $DB->query_first($total_sql);
    $pager = new pager($total['count']);

    $dicts_sql = sprintf("SELECT * FROM %s %s LIMIT %d, %d", 'scws_dict', $where_str, $pager->offset, $pager->perpage);
    $dicts_query = $DB->query($dicts_sql);
    $dicts = array();
    while($dict = $DB->fetch_array($dicts_query)){
        $dicts[] = $dict;
    }

    $attr_sql = sprintf("SELECT DISTINCT attr FROM %s ", 'scws_dict');
    $attr_query = $DB->query($attr_sql);
    $attrs = array();
    while($attr = $DB->fetch_array($attr_query)){
        $attrs[] = $attr;
    }
} else if($action == 'setpositive'){
    $ids = $_GET['rows'];
    if(!empty($ids)){
        $update_sql = sprintf("UPDATE %s SET rating = 1 WHERE id IN (%s)", "scws_dict", implode(',', $ids) );
        $DB->query($update_sql);
    }
    exit();
} else if($action == 'setnegative'){
    $ids = $_GET['rows'];
    if(!empty($ids)){
        $update_sql = sprintf("UPDATE %s SET rating = -1 WHERE id IN (%s)", "scws_dict", implode(',', $ids) );
        $DB->query($update_sql);
    }
    exit();
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
	  <li class="active">
	    <a href="index.php">管理词典</a>
	  </li>
	  <li><a href="scws_test.php">指数测试</a></li>
	</ul>
    <table class="table">
      <thead>
        <tr>
          <th class="span2">
      		Operation
          </th>
          <th>ID</th>
          <th>WORD</th>
          <th>TF/IDF</th>
          <th>&nbsp;</th>
          <th>RATING</th>
        </tr>
        <tr>
            <th class="span2">
      		<input type="checkbox" id="check_all">
      		<div class="btn-group">
			    <button class="btn dropdown-toggle" data-toggle="dropdown">
			      Action
			      <span class="caret"></span>
			    </button>
			    <ul class="dropdown-menu">
			      <li><a href="javascript:void(0)" id="set_positive">Positive</a></li>
                  <li><a href="javascript:void(0)" id="set_negative">Negative</a></li>                 
			    </ul>
			</div>
          </th>
          <th>&nbsp;</th>
          <th>
             <form action="" method="get" class="form-inline form-horizontal">
                <input type="text" placeholder="Search Word…" name="word" value="" class="input-medium search-query" />
                <a class="btn" href="index.php">Reset</a>
            </form>
          </th>
          <th>&nbsp;</th>
          <th>
            <select class="inline span1" id="attr_select">
                <?php foreach($attrs as $attr): ?><option value="<?php echo $attr['attr']; ?>"><?php echo $attr['attr']; ?></option><?php endforeach; ?>
            </select>
          </th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      
      <tbody>
      <?php foreach($dicts as $dict): ?>
        <tr class="<?php if($dict['rating'] > 0){echo 'success';}elseif($dict['rating'] < 0){echo 'error';} ?>">
          <td><label class="checkbox"><input type="checkbox" name="rows[]" value="<?php echo $dict['id'] ?>"></label></td>
          <td><?php echo $dict['id'] ?></td>
          <td><?php echo $dict['word'] ?></td>
          <td><?php echo $dict['tf'] . '/' . $dict['idf'] ?></td>
          <td><?php echo $dict['attr'] ?></td>
          <td class="rating"><?php echo $dict['rating'] ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>


    <div class="pagination pagination-centered">
      <?php echo $pager->result['pagenav']; ?>
	</div>

    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script>
    (function($){
    	$('#check_all').click(function(){
    		if($(this).prop('checked') === true){
    			$('input[name="rows[]"]').prop('checked', true);
    		}else{
    			$('input[name="rows[]"]').prop('checked', false);
    		}
    	});
        
        $('#attr_select').change(function(){
            var reg=new RegExp("[\?&]attr=.*","g"), href = window.location.href; //创建正则RegExp对象 
            href=href.replace(reg,"");   
            if(href.indexOf('?') === -1){
                href=href+'?attr='+this.value;
            }else{
                href=href+'&attr='+this.value;
            }
            window.location.href = href;
    	});
        
    	$('#set_positive').click(function(){
            $.get('index.php?action=setpositive', $('input[name="rows[]"]:checked').serialize(), function(data){
                console.log(data);
                $('input[name="rows[]"]:checked').parents('tr').removeClass().addClass('success');
                $('input[name="rows[]"]:checked').parents('tr').children('.rating').html('1');
            });
    	});
        
        
    	$('#set_negative').click(function(){
            $.get('index.php?action=setnegative', $('input[name="rows[]"]:checked').serialize(), function(data){
                console.log(data);
                $('input[name="rows[]"]:checked').parents('tr').removeClass().addClass('error');
                $('input[name="rows[]"]:checked').parents('tr').children('.rating').html('-1');
            });
    	});
    })(jQuery)
    </script>
  </body>
</html>