<?php

namespace Nfilin\Libs\Yii;

use yii\db\ActiveQueryInterface as YiiActiveQueryInterface;


/**
 * @inheritdoc
 */
interface ActiveQueryInterface extends YiiActiveQueryInterface
{

    /**
     * @inheritdoc
     * @return ActiveListInterface
     */
    function populate($rows);


    /**
     * @inheritdoc
     */
    function one($db = null);

}