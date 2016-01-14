<?php

namespace Nfilin\Libs\Yii;

use Yii;
//use yii\db\ActiveRecord as YiiAR;
use sjaakp\spatial\ActiveRecord as YiiAR;
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
        $table = static::tableName();
        $columns =  array_keys(static::getTableSchema()->columns);

        foreach ($formats as $column => $format) {
            if(!empty($constFormats[$format]))
                $formats[$column] = $constFormats[$format];
        }
        foreach ($columns as $column) {
            if(empty($formats[$column]))
                $select[] = "{$table}.{$column}";
            elseif($formats[$column] == '%s')
                $select[$column] = new Expression(sprintf($formats[$column], $column));
            else
                $select[$column] = new Expression(sprintf($formats[$column], "{$table}.{$column}"));
        }
        return $query->select($select);
    }

    public function afterFind(){
        parent::afterFind();        
        $constWrappers = self::COLUMN_WRAPPERS;
        foreach (static::formats() as $column => $format) {
            if(!empty($constWrappers[$format]) && is_callable($constWrappers[$format]))
                $this->{$column} = call_user_func($constWrappers[$format], $this->{$column});
        }
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

    const COLUMN_POINT = 'point';



    /**
     * Fromaters map
     */
    const COLUMN_FORMATS = [
        self::COLUMN_TIMESTAMP => self::FORMAT_TIMESTAMP,
        self::COLUMN_POINT => '%s',
    ];	

    const COLUMN_WRAPPERS = [
        self::COLUMN_POINT => ['self','point2array']
    ];

    const COLUMN_BEFORE_SAVE = [
        self::COLUMN_POINT => ['self','array2point']
    ];

    protected $__saved = [];
    
    public function beforeSave($insert)    {
        $constWrappers = self::COLUMN_BEFORE_SAVE;
        foreach (static::formats() as $column => $format) {
            if(!empty($constWrappers[$format]) && is_callable($constWrappers[$format])){
                $saved = $this->getAttribute($column);
                if($saved){                    
                    $this->_saved[$column] = $saved;
                    $this->setAttribute($column, call_user_func($constWrappers[$format], $saved));
                }
            }
        }
        return parent::beforeSave($insert);
    }

    static protected function array2point($val){
        return empty($val) ? null :  json_encode([
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => $val,
            ],
        ]);
    }
    static protected function point2array($val){
            if(empty($val))
                return null;
            try {
                return json_decode($val)->geometry->coordinates;
            } catch (\Exception $e) {
                return null;
            }
        }

    /**
     * Batch insert
     * @param array $columns The column names
     * @param array $rows The rows to be batch inserted into the table
     * @param bool|false $handle_error
     * @param false|bool|string $on_duplicate
     * @return integer Number of rows affected by the execution.
     * @see \yii\db\Command::batchInsert
     * @throws IntegrityException
     */
    static public function batchInsert($columns, $rows, $handle_error = false, $on_duplicate = false) {
        if(empty($rows))
            return false;
        
        try {
            $command = self::getDb()->createCommand();
            $sql = $command->db->getQueryBuilder()->batchInsert(static::tableName(), $columns, $rows);
            if($on_duplicate){
                $sql .= 'ON DUPLICATE KEY UPDATE '. $on_duplicate;
            }
            $command->setSql($sql);
            return $command->execute();
        } catch (IntegrityException $e) {
            if($handle_error === false)
                throw $e;                
        }
    }
}