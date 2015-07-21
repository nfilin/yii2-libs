<?php

namespace Nfilin\Libs\Yii;

use ArrayIterator;
use \yii\base;

/**
 * 
 */
class ActiveList extends ArrayIterator implements ActiveListInterface {

	/**
	 * @param ActiveRecord[]|ActiveRecord|ActiveListInterface $data
	 * @param int $flags
	 */
	function __construct($data = [], $flags = 0){
		parent::__construct([],$flags);
		if(is_array($data)){
			foreach ($data as $value) {
					if($value instanceof ActiveRecord)
						$this->append($value);
				}
		} elseif( is_object($data)){
			if($data instanceof ActiveRecordsList){
				foreach ($data as $value) {
					if($value instanceof YiiActiveRecord)
						$this->append($value);
				}
			} elseif($data instanceof ActiveRecord) {
				$this->append($data);
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	static function create($data = [], $flags = 0) {
		return new static($data, $flags);
	}

	/**
	 * @inheritdoc
	 */
	public static function className()
    {
        return get_called_class();
    }

    /**
	 * @inheritdoc
	 */
    function toArray() {
    	return $this->getArrayCopy();
    }

}
