<?php 
require_once 'functions/main.php';
require_once 'functions/auth.php';
require_once 'functions/db.php';
require_once 'functions/doc_fns.php';
startSession();
if (!access_check([],[],2)) exit ('Access denied');
do_page_header('Documents and forms','');
?>
<div id="wrap" onclick="cancel()"></div>
<style>
.korpus > div, .korpus > input { display: none;}
.korpus {text-align:left; padding: 0;margin:0;}
.korpus_div{width:100%; padding: 0;margin:0;}
.tab_menu{position: fixed;background-color: activecaption; width:100%; line-height: 15px;padding: 5px;}
.content{width:100%; position:relative; top:40px;}

.korpus label { padding: 5px; border: 1px solid #aaa; line-height: 28px; cursor: pointer; position: relative; bottom: 1px; background: #fff; font-weight: bold;}
.korpus input[type="radio"]:checked + label { border-bottom: 2px solid #fff; }

.korpus > input:nth-of-type(1):checked ~ div:nth-of-type(1),
.korpus > input:nth-of-type(2):checked ~ div:nth-of-type(2),
.korpus > input:nth-of-type(3):checked ~ div:nth-of-type(3),
.korpus > input:nth-of-type(4):checked ~ div:nth-of-type(4) { display: block; border: none; }
</style>
<div id="main_div_menu2">
<div class="korpus">
  <input type="radio" name="odin" checked="checked" id="vkl1"/><label for="vkl1">1. Documents</label><input type="radio" name="odin" id="vkl2"/><label for="vkl2">2. Job descriptions</label><input type="radio" name="odin" id="vkl3"/><label for="vkl3">3. Procedures</label><input type="radio" name="odin" id="vkl4"/><label for="vkl4">5. Document forms</label>
  <div class="korpus_div">
      <div class="tab_menu"><a href="#" class="knopka" onclick="display('')">New document</a></div>
      <div class="content">
      </div>
  </div>
  <div class="korpus_div">
      <div class="tab_menu"><a href="#" class="knopka" onclick="display('j_desc')">New document</a></div>
      <div class="content">
        <table id="doc_control" class="sortable" width="100%" border="1px" align="center">
        <thead>
            <th width=30>ID</th><th width=150>Type</th><th>Description</th><th width=150>Date</th><th width=150>Expire date</th>
            <th width=100>Days left</th><th width=100>Incharge</th><th width=100>Download</th>
        </thead>
        <tbody align="center">
<?php 
$time= time();
$query= 'select * from documents LEFT JOIN users ON incharge=username order by expire_date';
$db =  db_connect();
$result=$db->query($query);
if (!$result){
    echo "Ошибка выполнения запроса: " . $db->error;
    exit();
}
while($row = $result->fetch_assoc()){
    $days=floor(($row['expire_date']-$time) / (60*60*24));
    if ($days>$row['alarm']) $color='background: lime';
    else $color='background: yellow';
    if ($days<0) $color='background: red';
    
    echo '<tr style="'.$color.'">';
    echo '<td>'.view_doc_link($row['id']).'</td>'
        . '<td>'.$row['type'].'</td>'
        . '<td>'.$row['doc_name'].'</td>'
        . '<td>'.date('Y-m-d',$row['start_date']).'</td>'
        . '<td>'.date('Y-m-d',$row['expire_date']).'</td>'
        . '<td>'.$days.'</td>'
        . '<td>'.$row['full_name'].'</td>'
        . '<td>';
        if ($row['file']!=='*NO_FILE*')echo create_download_button($row['id'],$row['file']);
        echo '</td></tr>';
}
?>
        </tbody>
        </table>
      </div>
  </div>
  <div class="korpus_div">
      <div class="tab_menu"><a href="#" class="knopka" onclick="display('')">New document</a></div>
      <div class="content">
          Третья вкладка
      </div>
  </div>
  <div class="korpus_div">
      <div class="tab_menu"><a href="#" class="knopka" onclick="display('')">New document</a></div>
      <div class="content">
          Четвертая вкладка
      </div>
  </div>
</div>
</div>

<div class="hidden" id="j_desc" style="width: 500px; height: 400px;">
<h2 align="center">Add new document</h2>
    <form enctype="multipart/form-data" action="doc_forms_upload_jdesc.php" method="POST">
        <table width="100%" align="center" border="1px" cellspacing = "0" cellpadding="2px">
        <tr>
            <td><b>Document name</b></td>
            <td><input type="text" name="doc_name" required maxlength="100"></td>
        </tr>
        <tr>
            <td><b>Document date</b></td>
            <td><input type="text" name="doc_date" class="datepicker" value="<?php echo date('Y-m-d');?>"></td>
        </tr>
        <tr>
            <td><b>Document description</b></td>
            <td><textarea name="doc_desc"required maxlength="300" rows="6" cols="50"></textarea></td>
        </tr>
        </table>
    <div align="right" width="100%" style="padding: 10px">
    <input type="submit" value="Add document" onclick="return check_file(this)">
    <input type="button" value="Close" onclick="cancel()"> 
    </div>
        <input type="hidden" name="MAX_FILE_SIZE" value="3000000">
        <input type="hidden" name="cat" value="2">
        Send this file: <input id="file" name="userfile" type="file">
    </form>
</div>
<script type="text/javascript" src="java/java_func.js"></script>