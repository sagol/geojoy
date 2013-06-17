<?php

namespace app\modules\avatarField\models;

class UploadAvatar extends \app\components\UploadHandlerFields {


	protected function get_file_object($file_name) {
		$file_path = $this->options['upload_dir'] . $file_name;
		if(is_file($file_path) && $file_name[0] !== '.' && strpos($file_name, 'webcam_') === false) {
			$file = new \stdClass();
			$file->name = $file_name;
			$file->size = filesize($file_path);
			$file->url = $this->options['upload_url'].rawurlencode($file->name);
			foreach($this->options['image_versions'] as $version => $options) {
				if (is_file($options['upload_dir'].$file_name)) {
					$file->{$version.'_url'} = $options['upload_url']
						.rawurlencode($file->name);
				}
			}
			$this->set_file_delete_url($file);
			return $file;
		}
		return null;
	}


}