<?php

namespace Nfilin\Libs\Yii;

/**
 * Interface ActiveListInterface
 * @package Nfilin\Libs\Yii
 */
interface ActiveListInterface {

	/**
	 * @param array|ActiveRecord|ActiveListInterface $data
	 * @param int $flags
	 * @return ActiveListInterface
	 */
	static function create($data = [], $flags = 0);	

	/**
	 * @return string
	 */
	static function className();

    /**
	 * @return array
	 */
	function toArray();
}