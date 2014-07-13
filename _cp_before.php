<?php
class cp_microtime {
	public function cp_before(){
		static $cp_microtime_start;
		if ($cp_microtime_start == ''){
			$cp_microtime_start = microtime(true);
		}
		return $cp_microtime_start;
	}
}
$cp_microtime = new cp_microtime;
$cp_microtime->cp_before();
?>