<?php

namespace Nfilin\Libs\Yii;

use Nfilin\Libs\File as NfilinFile;
use yii\base\InvalidParamException;
use yii\helpers\FileHelper;

/**
  * @inheritdoc
  */
class File extends NfilinFile {

	/**
	 * @inheritdoc
	 */
	function upload($dir = null, $name = null){
		if($dir === null)
			return false;
		FileHelper::createDirectory($dir);
		$this->name = empty($name) ? $this->generateName() : $name;
		$target = FileHelper::normalizePath($dir . '/' . $this->name);
		//throw new \Exception($target);
		if(!move_uploaded_file($this->tmp_name, $target))
			return false;
		$this->tmp_name = $target;
		return true;
	}

	function generateName() {
		if(!file_exists($this->tmp_name))
			return null;
		$extension = FileHelper::getExtensionsByMimeType($this->type);
		$extension = empty($extension) ? '' : $extension[0];
		//throw new InvalidParamException(print_r($extension,true));
		$hash = sha1_file($this->tmp_name);
		return  "{$hash}.{$this->size}.{$extension}";

	}
}