<?php

namespace Nfilin\Libs\Yii;

use \yii\db|ActiveQuery as YiiActiveQuery;
use \yii\db\Connection;

/**
 * @inheritdoc
 */
class ActiveQuery extends YiiActiveQuery implements ActiveQueryInterface{

	/**
	 * @inheritd
	 * @return ActiveList
	 */
	public function populate($rows){
		$listClass = call_user_func([$this->modelClass, 'listClass']);
		$rows = parent::populate($rows);
		$rows = new $listClass($rows);//call_user_func([$listClass,'create'],$rows);
		//error_log(print_r([$this->modelClass, $listClass,$rows], true));
		return $rows;
	}

	/**
	 * @param Connection $db
	 * @return ActiveRecord|null
	 */
	public function one($db = null){
		$row = parent::one($db);
		return $row;
	}
}