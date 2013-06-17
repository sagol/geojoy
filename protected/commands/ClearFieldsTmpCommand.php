<?php

/**
 * Удаление временных файлов из папки загрузки 
 */
class ClearFieldsTmpCommand extends CConsoleCommand {


	public function run($args) {
		\Yii::setPathOfAlias('webroot', \Yii::getPathOfAlias('app') . DS . '..' . DS . 'html');

		$uploadTmpDir = \Yii::getPathOfAlias(\Yii::app()->params['uploadDir']) . DS . 'fields' . DS . 'tmp';

		$this->delFiles($uploadTmpDir, false);


		return 0; // всё хорошо, выходим с кодом 0
	}


	protected function delFiles($dir, $rmdir = true) {
		if($opendir = opendir($dir)) {
			$curDate = new \DateTime();

			while(($file = readdir($opendir)) !== false) {
				if($file == '.' || $file == '..') continue;

				$path = $dir . DS . $file;
				if(is_dir($path)) $this->delFiles($path);
				elseif(is_file($path)) {
					$date = new \DateTime(date("Y-m-d H:i:s", filemtime($path)));
					$interval = $date->diff($curDate);
					if($interval->days >= 1) unlink($path);
					else $rmdir = false;
				}
			}

			if($rmdir) rmdir($dir);
			closedir($opendir);
		}
	}


}