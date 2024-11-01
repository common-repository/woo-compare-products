<?php
class dateWcp {
	static public function _($time = NULL) {
		if(is_null($time)) {
			$time = time();
		}
		return date(WCP_DATE_FORMAT_HIS, $time);
	}
}