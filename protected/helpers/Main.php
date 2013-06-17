<?php

namespace app\helpers;

/**
 * Хелпер общий
 */
class Main {


	/**
	 * Генерация произвольной строки
	 * @param integer $len
	 * @return string 
	 */
	public static function randomString($len) {
		$string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

		for($i = 0; $i < $len; $i++) {
			$char = $string[mt_rand(0, 61)];
			if(strpos(@$return, $char) === false) @$return .= $char;
			else $i--;
		}


		return $return;
	}


}