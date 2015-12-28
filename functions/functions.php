<?php
/**
 * 查询字符是否存在于某字符串
 * @param $haystack 字符串
 * @param $needle 要查找的字符
 * @return bool
 */
function str_exists($haystack, $needle){
	return !(strpos($haystack, $needle) === FALSE);
}

/**
 * 取得文件扩展
 * @param $filename 文件名
 * @return 扩展名
 */
function fileext($filename) {
	return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}

/**
 * 
 * post
 * @param $arg
 */
function p($arg){
	if(isset($_POST[$arg])) {
		if(is_array($_POST[$arg])){
			return $_POST[$arg];
		}else{
			if(is_string($_POST[$arg])){
				$str = mbStrreplace($_POST[$arg]);
				if(get_magic_quotes_gpc()) {
					$str= stripslashes($str);
				}
				return mysql_escape_string($str);
			}else{
				return ;
			}
		}
	}else{
		return ;
	}
}

/**
 * 设置Get方法
 * 2011-11-11 by cyg
 * @param $arg 名称
 */
function g($arg){
	if (isset($_GET[$arg])) {
		if(is_array($_GET[$arg])){
			return $_GET[$arg];
		}else{
			if(is_string($_GET[$arg])){
				$str = mbStrreplace($_GET[$arg]);
				if(get_magic_quotes_gpc()) {
					$str= stripslashes($str);
				}
				return mysql_escape_string($str);
			}else{
				return ;
			}
		}
	}else{
		return ;
	}
}


/**
 * 
 * 字体转换
 * by guoxuanfeng
 * @param $content 需要能转换的字符
 * @param $from_encoding 转换前的编码
 * @param $to_encoding 转换后的编码
 */
function mbStrreplace($content,$from_encoding="UTF-8",$to_encoding="UTF-8") {
	$content= iconv($from_encoding,$to_encoding,$content);
	$str = iconv($from_encoding,$to_encoding,"　");
	$content = str_replace($str," ",$content);
	$content = iconv($from_encoding,$to_encoding,$content);
	$content = trim($content);
	return $content;
}

/**
 * 
 * 获取cookie
 * 2011-12-13
 * @param $arg cookie名称
 */
function c($arg) {
		if (isset($_COOKIE[$arg])) {
	 	 return $_COOKIE[$arg];
	 	}else return ;
	}

/**
 * 
 * session
 * 2011-11-24 by cyg
 * @param $arg 查询的字段名称
 */
function s($arg) {
		if (isset($_SESSION[$arg])) {
	 	 return $_SESSION[$arg];
	 	}else return '';
	}

/**
 * 
 * 数据库类公共调用
 * 2011-11-4 by cyg
 */
function db() {
	$db = new mysql();
	$db->connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	mysql_query("set names utf8");
	return $db;
}

/**
 * 
 * 单表数据查询
 * 2011-11-10 by cyg
 * @param $table 数据表
 * @param $where 查询条件
 * @param $order 排序
 * @param $limit $limit 查询的条件，-1=查询所有的
 * @param $fields 查询的字段，数组格式
 */
function searchdb($table,$where,$order,$limit=10,$fields=array()) {
	$db=db();
	$where=sqlwhere($where);
	if($fields){
		$sql = 'SELECT '. implode(',',$fields).' FROM `'.$table.'` WHERE '.$where;
	}else{
		$sql = 'SELECT * FROM `'.$table.'` WHERE '.$where;
	}
	if($order){
		$sql .= ' ORDER BY '.$order;
	}
	if($limit != -1){
		$sql .=' LIMIT '.$limit;
	}
	if ($limit == 1) {
		$r=$db->fetch_one_array($sql);
		return $r;
	}
	$r=$db->fetch_all($sql,2);
	return $r;
}


/**
 * 
 * 多表数据查询
 * 2011-11-10 by cyg
 * @param $sql 组合查询语句
 * @param $where Where条件
 * @param $order 排序条件
 * @param $limit 查询的条件，-1=查询所有的
 * @param $groupby groupby条件
 */
function searchmudb($sql,$where,$order,$limit=10,$groupby='') {
	$db=db();
	$where=sqlwhere($where);
	$query = $sql.' where '.$where;
	if($groupby){
		$query .= ' GROUP BY '.$groupby;
	}
	
	if($order){
		$query .= ' ORDER BY '.$order;
	}
	
	if ($limit != -1 ) {
		$query .=' LIMIT '.$limit;
	}
	if ($limit == 1) {
		$r=$db->fetch_one_array($query);
		return $r;
	}
	$r=$db->fetch_all($query,2);
	return $r;
}

/**
 * 
 * 多表数据查询 带groupby条件
 * 2011-11-10 by cyg
 * @param $sql 组合查询语句
 * @param $where Where条件
 * @param $groupby groupby条件
 * @param $orderby 排序条件
 * @param $limit 查询的条件，-1=查询所有的
 */
function search_by_group($sql,$where,$groupby,$orderby,$limit=10) {
	$db=db();
	$where=sqlwhere($where);
	if ($limit==-1) {
		$query=$sql.' WHERE '.$where.' GROUP BY '.$groupby.' ORDER BY '.$orderby;
	}else{
		$query=$sql.' WHERE '.$where.' GROUP BY '.$groupby.' LIMIT '.$limit;
	}
	$r=$db->fetch_all($query,2);
	return $r;
}


/**
 * 
 * 组织查询条件
 * @param $where 查询的条件
 */
function sqlwhere($where) {
		if (is_array($where)) {
			$whereopt=1;
			foreach ($where as $key=>$value) {
				if ($value=='') continue;
				if (is_array($value) && count($value)==1){
					$whereopt.=" AND $value[0]";
					// continue;
				}elseif(strtolower($value)=='null'){
					$whereopt.=" AND `$key` IS NULL";
				}elseif(is_numeric($value)){
					$whereopt.=" AND `$key`=$value";
				}else{
					$whereopt.=" AND `$key`='$value'";
				}
			}
		}else {
			$whereopt=$where;
		}
		return $whereopt;
	}

/**
 * 
 * 查询数据库记录数
 * 2011-11-17 by cyg
 * @param $table 数据表
 * @param $where 查询条件
 */
function get_db_count($table,$where) {
	$db = db();
	$where = sqlwhere($where);
	$query = 'SELECT count(*) num FROM `'.$table.'` WHERE '.$where;
	$numrows = $db->fetch_one_array($query);
	return $numrows['num'];
}

/**
 * 添加数据到数据库2011-11-18 by cyg
 * @param $tablename 数据表名
 * @param $param 插入的数据,数组格式
 */
function add_data_to_db($tablename,$param) {
	$db=db();
	if (is_array($param)) {
		$dbkeyarr=array_keys($param);
		$dbvaluearr=array_values($param);
	}
	$dbkey='`'.implode($dbkeyarr,'`, `').'`';
	$dbvalue="'".implode($dbvaluearr,"','")."'";
	$sql="INSERT INTO `$tablename` ($dbkey) VALUES ($dbvalue);";
	$db->query($sql);
	$insert_id=$db->insert_id();
	return $insert_id;
}

/**
 * 
 * 删除数据2012-04-26 by cyg
 * @param $tablename 数据表名
 * @param $where 删除条件
 */
function del_data($tablename,$where) {
	$db=db();
	$where=sqlwhere($where);
	$sql="DELETE FROM `$tablename` WHERE $where";
	$db->query($sql);
	$r=$db->affected_rows();
	return $r;
}
/**************************
2012-04-26 by cyg
更新数据
**************************/
function update_db($tablename,$param,$where) {
	$db=db();
	$str='';
	$where = sqlwhere($where);
	if (is_array($param)) {
		foreach ($param as $key=>$value) {
			if (is_numeric($value)) {
				$str.="`$key`='$value',";//2013-03-25调整 处理导入
			}elseif(strtolower($value)=='null'){
				$str.="`$key`=NULL,";
			}else{
				$str.="`$key`='$value',";
			}
		}
	}else{
		$str=$param;
	}
	$str = rtrim($str,',');
	
	if(strpos($where, 'AND') === FALSE) {
		return false;
	}
	$str="update `$tablename` set ".$str." where $where";
	$db->query($str);
	return $flag=$db->affected_rows();
}


/**
 * 
 * Logout
 * 2011-11-24 by cyg
 * 
 */
function logout() {
	unset($_SESSION);
	session_destroy();
	header('location: '.SITE_URL);exit;
}

/**************************
2011-11-11 by cyg
链接转向
**************************/
function redirect($url,$time='0',$tips='',$loc='self'){
	echo "<script>setTimeout(\"$loc.location.href='$url'\",'$time')</script><span id='tips'><a href='$url'>$tips</a></span>";
	exit;
}
/**************************
2011-11-11 by cyg
返回
**************************/
function redirectBack($tips=''){
	return  '<a href="Javascript:history.back()">'.$tips.'</a>';
}

/**************************
 *
* 发送邮件
*
* @param  $email string <p>接收邮件的地址</p>
* @param  $title string <p>邮件标题</p>
* @return $conetnt  string<p>邮件内容</p>
*
**************************/
function send_mail($email_info='cygsxak@163.com',$request,$server_flag=1,$mailcc=''){
	//测试机上不能发邮件，做额外处理，不在测试机上直接发送邮件
	if(defined('SERVER_ENVIRONMENT') && SERVER_ENVIRONMENT==0){
		$server_flag=0;
	}
	if(!$server_flag){//测试机
		$get_request=simplexml_load_string($request);
		$get_request = (array) $get_request;
		$request=json_encode($get_request);
		$content=rawurlencode($request);
		$email_info='cygsxak@163.com';
		$url = 'http://www.ebay.cn/phpMailertest/examples/foremail.php?email='.$email_info.'&content='.$content;
		file_get_contents($url);
	}else{
		$message = file_get_contents( ROOTPATH.'/includes/public/AMS_Email.html');
		$get_request=simplexml_load_string($request);
		$trans = array(
					'{request_type}'=>$get_request->request_type,
					'{request_status}'=>$get_request->request_status,
					'{requestor}'=>$get_request->requestor,
					'{create_date}' =>$get_request->create_date,
					'{link}' => '<a href="'.rawurldecode($get_request->link).'">Click here to view this change request</a>.',
					'{approval_status}' => $get_request->approval_status,
					'{update_date}' => $get_request->update_date,
					'{approval_ntid}' => $get_request->approval_ntid,
				);
		$message=strtr($message, $trans);
		// exit;
		$preg = "|<Subject>(.*)</Subject>\r\n<body>(.*)</body>|iUs";
		preg_match_all($preg, $message, $arr);
		// print_r($arr);
		// exit;
		unset($message);
		$subject=$arr[1][0];
		$message=$arr[2][0];
		// exit;
		// echo strtr($message, $trans);	
		$to      = $email_info;//后期做扩展
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$mailcc && $headers.= "Cc: " . $mailcc ."\r\n";
		$headers .= 'From: AMS <no-replay@mail.sourcingforebay.cn>' ;
		mail($to,$subject, $message,$headers);
	}
}


/**
 * 
 * 检查是否正确的email
 * 2012-10-08 by cyg
 * 
 * @param $email Email地址
 */
function is_email($email){
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		return false;
	}else{
		return true;
	}
}


/**
 * 
 * 获取当前时间
 * 2013-10-09 by cyg
 * 
 */
function time_now(){
	return date('Y-m-d H:i:s');
}

/**
 * 
 * 换换XML，增加为CDATA形式
 * @author CHEN041
 *
 */
class SimpleXMLExtended extends SimpleXMLElement {
  public function addCData($cdata_text) {
    $node = dom_import_simplexml($this); 
    $no   = $node->ownerDocument; 
    $node->appendChild($no->createCDATASection($cdata_text)); 
    }
}


/**
 * 
 * array2xml
 * @param $array
 * @param $xml
 */
function array2xml($array, $xml = false){
    if($xml === false){
        $xml = new SimpleXMLExtended('<root/>');
    }
    foreach($array as $key => $value){
        if(is_array($value)){
            array2xml($value, $xml->addChild($key));
        }else{
			//如果包含汉字，转编码
			if (preg_match("/([\x81-\xfe][\x40-\xfe])/", $value, $match)) {
				$value = iconv('gbk', 'utf-8', $value);   
			}
			$xml->$key = NULL; // VERY IMPORTANT! We need a node where to append
			$xml->$key->addCData($value);
			//$xml->$key->addAttribute('lang', 'en');
           // $xml->addChild($key, $value);
        }
    }
    return $xml->asXML();
}

/* xml 字符串转成数组 */
function xml2array($templates_content){
	if(!$templates_content) return array();
	return json_decode(json_encode((array) @simplexml_load_string($templates_content, null, LIBXML_NOCDATA)),1);
}


/**
 * 
 * 导出csv文件
 * 2013-10-15 oliver feng
 * 
 * @param $array
 */
function array2csv(array &$array){
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   fputcsv($df, array_keys(reset($array)));
   foreach ($array as $row) {
      fputcsv($df, $row);
   }
   fclose($df);
   return ob_get_clean();
}

/**
 * 
 * 解决fgetcsv 在 php5.2.8 中的bug
 * @param $handle
 * @param $length
 * @param $d
 * @param $e
 */
function __fgetcsv(& $handle, $length = null, $d = ',', $e = '"') {
     $d = preg_quote($d);
     $e = preg_quote($e);
     $_line = "";
     $eof=false;
     while ($eof != true) {
         $_line .= (empty ($length) ? fgets($handle) : fgets($handle, $length));
         $itemcnt = preg_match_all('/' . $e . '/', $_line, $dummy);
         if ($itemcnt % 2 == 0)
             $eof = true;
     }
     $_csv_line = preg_replace('/(?: |[ ])?$/', $d, trim($_line));
     $_csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
     preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
     $_csv_data = $_csv_matches[1];
     for ($_csv_i = 0; $_csv_i < count($_csv_data); $_csv_i++) {
         $_csv_data[$_csv_i] = preg_replace('/^' . $e . '(.*)' . $e . '$/s', '$1' , $_csv_data[$_csv_i]);
         $_csv_data[$_csv_i] = str_replace($e . $e, $e, $_csv_data[$_csv_i]);
     }
     return empty ($_line) ? false : $_csv_data;
}

/**
 * 
 * 文件下载处理，发送header
 * 
 * @param $filename 文件名称
 */
function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}


/**
 * 递归方式的对变量中的特殊字符进行转义
 * 2013-10-14 oliver feng
 * 
 * @access  public
 * @param   mix     $value
 * @return  mix
 */
function addslashes_deep($value){
	if (empty($value))
	{
		return $value;
	}
	else
	{
		return is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
	}
}

/**
 * 
 * 解析模板标签
 * 正则替换，后期可继续扩展 采用smarty方式解析处理
 * 
 * $istag 是否输出顶部内容 防止直接打开页面
 * @param $str 模板中的内容
 * @param $istag 是否加载阻止语句
 */
function template_parse($str, $istag = 0){
	$str = preg_replace("/([\n\r]+)\t+/s","\\1",$str);
	$str = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}",$str);
	$str = preg_replace("/\{include\s+(.+)\}/","<?php include \\1; ?>",$str);
	$str = preg_replace("/\{php\s+(.+)\}/","<?php \\1?>",$str);
	$str = preg_replace("/\{if\s+(.+?)\}/","<?php if(\\1) { ?>",$str);
	$str = preg_replace("/\{else\}/","<?php } else { ?>",$str);
	$str = preg_replace("/\{elseif\s+(.+?)\}/","<?php } elseif (\\1) { ?>",$str);
	$str = preg_replace("/\{\/if\}/","<?php } ?>",$str);
	$str = preg_replace("/\{loop\s+(\S+)\s+(\S+)\}/","<?php if(is_array(\\1)) foreach(\\1 AS \\2) { ?>",$str);
	$str = preg_replace("/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}/","<?php if(is_array(\\1)) foreach(\\1 AS \\2 => \\3) { ?>",$str);
	$str = preg_replace("/\{\/loop\}/","<?php } ?>",$str);
	$str = preg_replace("/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/","<?php echo \\1;?>",$str);
	$str = preg_replace("/\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/","<?php echo \\1;?>",$str);
	$str = preg_replace("/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/","<?php echo \\1;?>",$str);
	$str = preg_replace("/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/es", "addquote('<?php echo \\1;?>')",$str);
	$str = preg_replace("/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/s", "<?php echo \\1;?>",$str);
	if(!$istag) $str = "<?php defined('SITE_URL') or exit('Access Denied'); ?>".$str;
	return $str;
}

/**
 * 
 * 加载模板内容
 * 
 * @param $template_code 模板标识符
 * @param $templates_content 需要解析的内容
 */
function load_template($template_code='',$templates_content) {
	//print_r($templates_content);
	//处理传过来的参数
	$T_image=array();//图片展示
	$imageIndex=1;//图片索引从1开始
	foreach($templates_content as $k2=>$v2){
		if(is_array($v2)){
			$v2='';
		}
		if(stripos($k2,'T_Image')===0 and $v2){
			$T_image[$imageIndex]=$v2;
			$imageIndex++;
		}else{
			$$k2=$v2;
		}
//		echo "$$k2=$v2;<br>";
	}
	
	//样式单处理
	$T_image_number=count($T_image);
	//处理模板
	if (!$template_code) {
		$template_code='systems_tpl_1';
	}
	//样式单处理
	if (!isset($tpl_color)) {
		$stylesheet='style.css';
	}else{
		$stylesheet='style_'.strtolower($tpl_color).'.css';
	}
	$stylesheet=SITE_URL.'/style/template/'.$template_code.'/'.$stylesheet;
	
	$tpl_html_path=ROOTPATH.'/style/template/'.$template_code.'/tpl.html';//选择模板
	$tpl_php_path=ROOTPATH.'/style/template/'.$template_code.'/tpl.php';//选择模板
	if (!file_exists($tpl_html_path)) {
		exit('template not exists !');
	}
	$tpl_html_content=file_get_contents($tpl_html_path);
	
	//模板处理
	$tpl_html_content=str_ireplace('url(images/tp_', 'url('.SITE_URL.'/style/template/'.$template_code.'/images/tp_', $tpl_html_content);//处理背景
	$tpl_html_content=str_ireplace('"style.css"', '"<?php echo $stylesheet;?>"', $tpl_html_content);//样式单处理
	$template_content=template_parse($tpl_html_content,0);
	
	//缓存机制
	if (!file_exists($tpl_php_path)) {
		file_put_contents($tpl_php_path, $template_content);
	}
		
	ob_start();
	include $tpl_php_path;
	$content= ob_get_contents();
	ob_end_clean();
	
	return $content;
}

/**
 * 
 * 清空模板缓存处理
 * @param $tpl_id 模板目录标识符, 比如systems_tpl_1
 * 如果没有值，则将所有的模板缓存清空处理
 */
function refresh_template($tpl_id=''){
	if (!$tpl_id){
		$base_path=ROOTPATH."/style/template/*";
		$tpl_ids=glob($base_path,GLOB_ONLYDIR);
		foreach ($tpl_ids as $tpl_path) {
			$tpl_id=str_ireplace(ROOTPATH.'/style/template/', '', $tpl_path);
			refresh_template($tpl_id);
		}
	}else{
		$tpl_id=strtolower($tpl_id);
		$tpl_path=ROOTPATH."/style/template/$tpl_id/tpl.php";
		if (file_exists($tpl_path)){
			unlink($tpl_path);
		}
	}
}

/**
 * 
 * creates a compressed zip file
 * 
 * @param unknown_type $files
 * @param unknown_type $destination
 * @param unknown_type $overwrite
 */
function create_zip($files = array(),$destination = '',$overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
			$zip->addFile($file,basename($file));//去掉层级目录
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}

/**
 * 
 * 文件写入缓存
 * 2011-11-17 by cyg
 * 
 * @param $cachename 缓存标识
 * @param $content 缓存内容
 */
function write_to_file($cachename,$content = '') {
	if (is_array($content)) {
		$content = "\$_CACHE['$cachename'] = ".var_export($content,True).';';
	}
	$content = "<?php\n//该文件是系统自动生成的缓存文件，请勿修改\n//创建时间：".time_now()."\n".$content;
	$filename = ROOTPATH.'/cache/cache_'.$cachename.'.php';
	$len = file_put_contents($filename, $content);
	@chmod($filename, 0777);
	return $len;
}

/**
 * 
 * 读取缓存内容
 * 
 * 2011-11-17 by cyg
 * @param $cachename 缓存标识
 */
function get_cache($cachename) {
	global $_CACHE;
	!is_array($cachename) && $cachename = array($cachename);
	foreach ($cachename as $cache) {
		if (isset($_CACHE[$cache])) continue ;//不是缓存内容
		$cachefile = ROOTPATH.'/cache/cache_'.$cache.'.php';
		if(file_exists($cachefile)){
			include($cachefile);
		}
	}
	return ;
}


/**
 * 生成sql语句，如果传入$in_cloumn 生成格式为 IN('a', 'b', 'c')
 * 
 * @param $data 条件数组或者字符串
 * @param $front 连接符
 * @param $in_column 字段名称
 * @return string
 */
function to_sqls($data, $front = ' AND ', $in_column = false) {
	if($in_column && is_array($data)) {
		$ids = '\''.implode('\',\'', $data).'\'';
		$sql = "$in_column IN ($ids)";
		return $sql;
	} else {
		if ($front == '') {
			$front = ' AND ';
		}
		if(is_array($data) && count($data) > 0) {
			$sql = '';
			foreach ($data as $key => $val) {
				$sql .= $sql ? " $front `$key` = '$val' " : " `$key` = '$val' ";
			}
			return $sql;
		} else {
			return $data;
		}
	}
}

/**
 * 判断email格式是否正确
 * @param $email
 */
function is_mail($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}


/**
* 产生随机字符串
*
* @param    int        $length  输出长度
* @param    string     $chars   可选的 ，默认为 0123456789
* @return   string     字符串
*/
function random($length, $chars = '0123456789') {
	$hash = '';
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}

/**
 * 
 * 将字符串转换为数组
 *
 * @param	string	$data	字符串
 * @return	array	返回数组格式，如果，data为空，则返回空数组
 */
function string2array($data) {
	if($data == '') return array();
	@eval("\$array = $data;");
	return $array;
}


/**
* 将数组转换为字符串
* 
* @param	array	$data		数组
* @param	bool	$isformdata	如果为0，则不使用new_stripslashes处理，可选参数，默认为1
* @return	string	返回字符串，如果，data为空，则返回空
*/
function array2string($data, $isformdata = 1) {
	if($data == '') return '';
	if($isformdata) $data = new_stripslashes($data);
	return addslashes(var_export($data, TRUE));
}


/**
 * 
 * 转换字节数为其他单位
 * 
 * @param	string	$filesize	字节大小
 * @return	string	返回大小
 */
function sizecount($filesize) {
	if ($filesize >= 1073741824) {
		$filesize = round($filesize / 1073741824 * 100) / 100 .' GB';
	} elseif ($filesize >= 1048576) {
		$filesize = round($filesize / 1048576 * 100) / 100 .' MB';
	} elseif($filesize >= 1024) {
		$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
	} else {
		$filesize = $filesize.' Bytes';
	}
	return $filesize;
}


/**
 * 
 * 加密解密
 * 
 * @param $string
 * @param $operation "ENCODE"加密
 * @param $key
 * @param $expiry
 */
function sys_auth($string, $operation = 'ENCODE', $key = '', $expiry = 0) {
	$key_length = 4;
	$key = md5($key != '' ? 'cyg':'');
	$fixedkey = md5($key);
	$egiskeys = md5(substr($fixedkey, 16, 16));
	$runtokey = $key_length ? ($operation == 'ENCODE' ? substr(md5(microtime(true)), -$key_length) : substr($string, 0, $key_length)) : '';
	$keys = md5(substr($runtokey, 0, 16) . substr($fixedkey, 0, 16) . substr($runtokey, 16) . substr($fixedkey, 16));
	$string = $operation == 'ENCODE' ? sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$egiskeys), 0, 16) . $string : base64_decode(substr($string, $key_length));

	$i = 0; $result = '';
	$string_length = strlen($string);
	for ($i = 0; $i < $string_length; $i++){
		$result .= chr(ord($string{$i}) ^ ord($keys{$i % 32}));
	}
	if($operation == 'ENCODE') {
		return $runtokey . str_replace('=', '', base64_encode($result));
	} else {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$egiskeys), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	}
}


/**
 * 
 * 文件下载
 * 
 * @param $filepath 文件路径
 * @param $filename 文件名称
 */
function file_down($filepath, $filename = '') {
	if(!$filename) $filename = basename($filepath);
	if(is_ie()) $filename = rawurlencode($filename);
	$filetype = fileext($filename);
	$filesize = sprintf("%u", filesize($filepath));
	if(ob_get_length() !== false) @ob_end_clean();
	header('Pragma: public');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: pre-check=0, post-check=0, max-age=0');
	header('Content-Transfer-Encoding: binary');
	header('Content-Encoding: none');
	header('Content-type: '.$filetype);
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	header('Content-length: '.$filesize);
	readfile($filepath);
	exit;
}


/**
 * 
 * 检测输入中是否含有错误字符
 * 
 * @param char $string 要检查的字符串名称
 * @return TRUE or FALSE
 */
function is_badword($string) {
	$badwords = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#");
	foreach($badwords as $value){
		if(strpos($string, $value) !== FALSE) {
			return TRUE;
		}
	}
	return FALSE;
}

/**
 * 
 * 使用CURL抓取内容
 * 2013-11-7 by CHEN041
 *
 * @param $durl
 * 注意：此方法适用于纯粹获取网络地址信息，相当于get当时获取内容
 */
function curl_file_get_contents($url){
	if(function_exists('curl_init')){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);//设置访问的url地址
		curl_setopt($ch,CURLOPT_HEADER,0);//是否显示头部信息
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);//设置超时
		//curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);  //跟踪301
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//返回结果
		$r = curl_exec($ch);
		curl_close($ch);
		return $r;
	}elseif(function_exists('file_get_contents')){
		return file_get_contents($url);
	}else{
		return false;
	}
}


/**
 * 文件大小单位计算
 * @param $filesize 文件大小
 */

function size($filesize) {
	if($filesize >= 1073741824) {
		$filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
	} elseif($filesize >= 1048576) {
		$filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
	} elseif($filesize >= 1024) {
		$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
	} else {
		$filesize = $filesize . ' Bytes';
	}
	return $filesize;
}
