<?php

namespace Nfilin\Libs\Yii;

use yii\db\ActiveQuery as YiiActiveQuery;
//use sjaakp\spatial\ActiveQuery as YiiActiveQuery;
use yii\db\Connection;

/**
 * @inheritdoc
 */
class ActiveQuery extends YiiActiveQuery implements ActiveQueryInterface
{
    protected $batch_calls = [];

    /**
     * @param array $rows
     * @return array|\yii\db\ActiveRecord[]
     */
    public function populate($rows)
    {
        $listClass = call_user_func([$this->modelClass, 'listClass']);
        $rows = parent::populate($rows);
        foreach ($rows as $row) {
            foreach ($this->batch_calls as $call) {
                call_user_func($call, $row);
            }
        }
        $rows = new $listClass($rows);
        return $rows;
    }

    /**
     * @param Connection $db
     * @return ActiveRecord|null
     */
    public function one($db = null)
    {
        $row = parent::one($db);
        return $row;
    }

    /**
     * @param \yii\db\QueryBuilder $builder
     * @return \yii\db\Query
     * @throws \yii\base\InvalidConfigException
     */
    public function prepare($builder)
    {
        /** @var ActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        $schema = $modelClass::getTableSchema();
        if (empty($this->select)) {
            $this->select('*');
            foreach ($schema->columns as $column) {
                if (ActiveRecord::isSpatial($column)) {
                    $field = $column->name;
                    $this->addSelect(["AsText($field) AS $field"]);
                }
            }
        } else {
            foreach ($this->select as $column => $field) {
                $column = $schema->getColumn(is_numeric($column) ? $field : $column);
                if (ActiveRecord::isSpatial($column)) {
                    $this->addSelect(["AsText($field) AS $field"]);
                }
            }
        }
        return parent::prepare($builder);
    }

    /**
     * @param string $name
     * @param callable $func
     * @return $this
     */
    public function addBatchCall($name, callable $func)
    {
        $this->batch_calls[$name] = $func;
        return $this;
    }
}