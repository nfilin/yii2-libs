<?php

namespace Nfilin\Libs\Yii\data;


use Nfilin\Libs\Yii\ActiveList;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider as YiiActiveDataProvider;
use yii\db\QueryInterface;

class ActiveDataProvider extends YiiActiveDataProvider
{
    /**
     * @inheritdoc
     */
    protected function prepareModels()
    {
        if (!$this->query instanceof QueryInterface) {
            throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
        }
        $query = clone $this->query;
        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();
            $query->limit($pagination->getLimit())->offset($pagination->getOffset());
        }
        
        if (($sort = $this->getSort()) !== false) {
            $query->addOrderBy($sort->getOrders());
        }

        $data = $query->all($this->db);
        return $data instanceof ActiveList ? $data->toArray() : $data;

    }
}