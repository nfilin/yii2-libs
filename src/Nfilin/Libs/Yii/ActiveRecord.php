<?php

namespace Nfilin\Libs\Yii;

use Yii;
use yii\db\ActiveRecord as YiiAR;
use yii\db\Expression;
use yii\db\ActiveQueryInterface;
use yii\db\BaseActiveRecord;
use yii\db\IntegrityException;



/**
 * @inheritdoc
 */
abstract class ActiveRecord extends YiiAR implements ActiveRecordInterface{

    /**
     * @var array List of attributes and relations visibilities used by [[ActiveRecord::fields()]]
     */
    protected $attributesVisibility = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $formats = static::formats();
        foreach ($formats as $column => $format) {
            switch($format){
                case self::COLUMN_TIMESTAMP:
                    static::getTableSchema()->columns[$column]->phpType = 'integer';
                    break;
            }
        }
    }

    /**
     * @property array $attributesVisibility
     * @param string $key Property name
     * @param boolean $visible
     * @return ActiveRecord
     */
    public function setVisibilityMode($key, $visible = true){
        $this->attributesVisibility[$key] = $visible;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();
        //throw new \Exception(print_r($this->_hiddenAttributes,true));
        foreach ($this->attributesVisibility as $key => $visible) {
            if($visible == self::VISIBILITY_HIDDEN)
                unset($fields[$key]);
            else
                $fields[$key] = $key;
        }        
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public static function find(){
        $query = Yii::createObject(ActiveQuery::className(), [get_called_class()]);
        $formats = static::formats();
        $constFormats = self::COLUMN_FORMATS;
        if(empty($formats))
            return $query;

        $select = [];
        //error_log(print_r(static::getTableSchema()->columns,true));
        $columns = array_keys(static::getTableSchema()->columns);
        foreach ($formats as $column => $format) {
            if(!empty($constFormats[$format]))
                $formats[$column] = $constFormats[$format];
        }
        foreach ($columns as $column) {
            if(empty($formats[$column]))
                $select[] = $column;
            else 
                $select[$column] = new Expression(sprintf($formats[$column], $column));
        }
        return $query->select($select);
    }

    /**
     * @inheritdoc
     */
    public static function listClass(){
        $class = static::className().'sList';
        return class_exists($class) ? $class : ActiveList::className();
    }

    /**
      * 
      */
    public function asList() {
        return call_user_func([static::listClass(),'create'], $this);
    }

    /**
     * @inheritdoc
     */
    static function formats() {
        return [];
    }
   
    /**
     * Timestamp SQL formater
     */
    const FORMAT_TIMESTAMP = 'UNIX_TIMESTAMP(%s)';
   
    /**
     * Timestamp formater name
     */
    const COLUMN_TIMESTAMP = 'timestamp';

    /**
     * Fromaters map
     */
    const COLUMN_FORMATS = [
        self::COLUMN_TIMESTAMP => self::FORMAT_TIMESTAMP
    ];	

    /**
     * Batch insert
     * @param array $columns The column names
     * @param array $rows The rows to be batch inserted into the table
     * @return integer Number of rows affected by the execution.
     * @see \yii\db\Command::batchInsert
     */
    static public function batchInsert($columns, $rows, $handle_error = false) {
        if(empty($rows))
            return false;
        
        try {
            return self::getDb()->createCommand()->batchInsert(static::tableName(), $columns, $rows)->execute();
        } catch (IntegrityException $e) {
            if($handle_error === false)
                throw $e;                
        }
    }
}