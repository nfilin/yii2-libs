<?php

namespace Nfilin\Libs\Yii;

use ArrayIterator;
use \yii\base;
use \yii\base\InvalidCallException;
use \yii\base\UnknownPropertyException;

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
     * Returns the fully qualified name of this class.
     * @return string the fully qualified name of this class.
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

	/**
     * Returns the value of an object property.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `$value = $object->property;`.
     * @param string $name the property name
     * @return mixed the property value
     * @throws UnknownPropertyException if the property is not defined
     * @throws InvalidCallException if the property is write-only
     * @see __set()
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (method_exists($this, 'set' . $name)) {
            throw new InvalidCallException('Getting write-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * Sets value of an object property.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `$object->property = $value;`.
     * @param string $name the property name or the event name
     * @param mixed $value the property value
     * @throws UnknownPropertyException if the property is not defined
     * @throws InvalidCallException if the property is read-only
     * @see __get()
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Setting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }
    /**
     * Checks if a property is set, i.e. defined and not null.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `isset($object->property)`.
     *
     * Note that if the property is not defined, false will be returned.
     * @param string $name the property name or the event name
     * @return boolean whether the named property is set (not null).
     * @see http://php.net/manual/en/function.isset.php
     */
    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        } else {
            return false;
        }
    }
    /**
     * Sets an object property to null.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `unset($object->property)`.
     *
     * Note that if the property is not defined, this method will do nothing.
     * If the property is read-only, it will throw an exception.
     * @param string $name the property name
     * @throws InvalidCallException if the property is read only.
     * @see http://php.net/manual/en/function.unset.php
     */
    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Unsetting read-only property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     */
    public function merge(ArrayIterator $data){
        foreach ($data as $key => $value) {
            $this->offsetSet($key, $value);
        }
        return $this;
    }
}
