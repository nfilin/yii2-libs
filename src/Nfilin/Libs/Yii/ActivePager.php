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
	private $__hooks = [];
	/**
	 * @param QueryInterface $query
	 * @throw Exception If [[$query]] is not [[QueryInterface]]
	 */
	function __construct($query = null) {
		if(empty($query) || !($query instanceof QueryInterface))
			throw new Exception('Incorrect query');
		$this->query = $query;
	}

	function each($function, $params = []){
		$this->__hooks[] = [$function, $params];
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	function build(){
        $this->total_count  = (int) $this->query->count();
        $this->objects      = $this->query->limit($this->limit)->offset($this->offset)->all()->toArray();
        foreach ($this->__hooks as $__hook) {
        	/*if(is_callable($__hook[0])){

        	} else*/
        	if(count($this->objects) && method_exists($this->objects[0], $__hook[0])) {
	        	foreach ($this->objects as $key => $object) {
	        		call_user_func_array([$object, $__hook[0]], $__hook[1]);
	        	}
	    	}
        }
        return $this;
	}
}
