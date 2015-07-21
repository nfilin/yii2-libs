<?php

namespace Nfilin\Libs\Yii;

use Nfilin\Libs\Pager as ZeusPager; 
use yii\db\QueryInterface;
use yii\base\InvalidParamException as Exception;

/**
 * @inheritdoc
 * Uses [[QueryInterface]] as input data
 */
class ActivePager extends ZeusPager {

	/**
	 * @param QueryInterface $query
	 * @throw Exception If [[$query]] is not [[QueryInterface]]
	 */
	function __construct($query = null) {
		if(empty($query) || !($query instanceof QueryInterface))
			throw new Exception('Incorrect query');
		$this->query = $query;
	}

	/**
	 * @inheritdoc
	 */
	function build(){
        $this->total_count  = (int) $this->query->count();
        $this->objects      = $this->query->limit($this->limit)->offset($this->offset)->all()->toArray();
        return $this;
	}
}
