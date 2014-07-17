<?php
define (log_url, '_cp_test');
define (index_file, '_index');

header('Content-Type: text/html; charset=UTF-8');

class cp_show {

	var $tree = array();
	var $allCpTime = 0;
	var $allTime = 0;
	
	public function cp_scan_dir($path, $up=0)
	{
		if ($up>0)
		{
			$expl = explode('/', $path);
			$count = count ($expl);
			$path = '';
			for($i=0; $i<$count-$up-1; $i++)
			{
				$path.=$expl[$i].'/';
			}
		}
		
		if ($files = scandir($path))
		{
			$count = count($files);
			for($i=2; $i<$count; $i++)
			{
				if (is_file($path.$files[$i]))
				{
					$file['name'] = $files[$i];
					$file['url'] = $this->get_url($path.$files[$i]);
					$file['value'] = unserialize(file_get_contents($path.$files[$i]));
					$res['files'][] = $file;
					$this->tree[] = $file;
					
					$this->allCpTime += $file['value']['time'];
					
					// echo '<pre>';
					// var_dump($path.$files[$i]);
					// echo '</pre>';
					// break;
				}
				else
				{
					$dir['name'] = $files[$i];
					$dir['subdir'] = $this->cp_scan_dir($path.$files[$i].'/');
					$res['dir'][] = $dir;
				}
			}
			$res['path'] = $path;
			return($res);
		}
		else return false;
	}
	
	function get_url($path){
		$path = str_replace ($_SERVER['DOCUMENT_ROOT'].'/'.log_url, '', $path);
		$path = preg_replace ('/'.index_file.'$/', '', $path);
		return $path;
	}

}

$ini = unserialize(file_get_contents ($_SERVER['DOCUMENT_ROOT'].'/_cp_init'));
$cp_show = new cp_show;
$tree = $cp_show->cp_scan_dir($_SERVER['DOCUMENT_ROOT'].'/'.log_url.'/');

//$timezone = DateTime::getTimezone();
//$date = new DateTime(null, new DateTimeZone('Europe/London'));
//86400	//секунд в сутках
//time() - $ini['time']	//время тестирования
//$cp_show->allCpTime		//время выполнения скриптов

$dayTime = 86400 / (time() - $ini['time']) * $cp_show->allCpTime;

?>
<html>
	<head>
		<style>
			.treeTable td{
				border: 1px solid #ccc;
				padding: 0px 10px;
			}
		</style>
	</head>
	<body>
		<div>
			<p><b>Общее время тестирования:</b> <?php echo gmdate('H:i:s', time() - $ini['time']); ?></p>
			<p><b>Суммарное время выполнения скриптов:</b> <?php echo gmdate('H:i:s', $cp_show->allCpTime); ?> (<?php echo number_format($cp_show->allCpTime, 3, '.', ' '); ?> сек.)</p>
			<p><b>Ориентировочное, суммарное время выполнения скриптов за сутки:</b> <?php echo gmdate('H:i:s', $dayTime); ?> (<?php echo number_format($dayTime, 3, '.', ' '); ?> сек.)</p>
		</div>
		<table class="treeTable">
			<tr>
				<td>url</td>
				<td>Посещений</td>
				<td>Общее время</td>
				<td>Макс. время</td>
				<td>Мин. время</td>
				<td>Средн. время</td>
			</tr>
<?php
foreach ($cp_show->tree as $branc){ ?>
			<tr>
				<td><?php echo $branc['url']; ?></td>
				<td><?php echo $branc['value']['count']; ?></td>
				<td align="right"><?php echo number_format ($branc['value']['time'], 3, '.', ' '); ?></td>
				<td align="right"><?php echo number_format ($branc['value']['maxTime'], 3, '.', ' '); ?></td>
				<td align="right"><?php echo number_format ($branc['value']['minTime'], 3, '.', ' '); ?></td>
				<td align="right"><?php echo number_format ($branc['value']['time'] / $branc['value']['count'], 3, '.', ' '); ?></td>
			</tr>
<?php
}
?>
		</table>
	</body>
</html>