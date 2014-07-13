<?php
$cp_microtime = new cp_microtime;
$cp_microtime_time = microtime(true) - $cp_microtime->cp_before();

//$filePath = $_SERVER['DOCUMENT_ROOT'].'/_cp_test'.$_SERVER['SCRIPT_NAME'];
$filePath = $_SERVER['DOCUMENT_ROOT'].'/_cp_test'.$_SERVER['REQUEST_URI'];
$filePath .= (preg_match('/\/$/', $filePath))?'_index':'';


if (!is_file($filePath)){
	//$dirArray = explode ('/', $_SERVER['SCRIPT_NAME']);
	$dirArray = explode ('/', $_SERVER['REQUEST_URI']);
	unset($dirArray[0]);
	unset($dirArray[count($dirArray)]);
	
	$dir = $_SERVER['DOCUMENT_ROOT'].'/_cp_test';
	foreach($dirArray as $v){
		$dir .= '/'.$v;
		mkdir($dir);
	}
}

$old = file_get_contents($filePath);
if ($old != ''){
	$old = unserialize($old);
}
$old['time'] = (float)$old['time'];
$old['count'] = (int)$old['count'];
$old['maxTime'] = (float)$old['maxTime'];
$old['minTime'] = (float)$old['minTime'];

$new['time'] = $old['time'] + $cp_microtime_time;
$new['count'] = $old['count'] + 1;
$new['maxTime'] = ($old['maxTime'] > $cp_microtime_time) ? $old['maxTime'] : $cp_microtime_time;
$new['minTime'] = ($old['minTime'] > $cp_microtime_time or $old['minTime'] == 0) ? $cp_microtime_time : $old['minTime'];

$new = serialize($new);
$cpFile = fopen($filePath, 'w');
fwrite ($cpFile, $new);
fclose($cpFile);
//printf('Скрипт выполнялся %.5F сек.', $cp_microtime_time);
?>