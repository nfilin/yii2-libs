<?php

namespace Nfilin\Libs\Yii;

use yii\db\ActiveQueryInterface as YiiActiveQueryInterface;


/**
 * @inheritdoc
 */
interface ActiveQueryInterface extends YiiActiveQueryInterface {

	/**
	 * @inheritd
	 * @return ActiveListInterface
	 */
	function populate($rows);


	/**
	 * @inheritdoc
	 */
	function one($db = null);

}