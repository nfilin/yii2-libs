<?php

namespace Nfilin\Libs\Yii;

use yii\db\ActiveRecordInterface as YiiActiveRecordInterface;
use yii\db\BaseActiveRecord;

/**
 * @inheritdoc
 */
interface ActiveRecordInterface extends YiiActiveRecordInterface
{
    /**
     * @param string $key
     * @param boolean $visible
     * @return ActiveRecordInterface
     */
    function setVisibilityMode($key, $visible = true);

    /**
     * @inheritdoc
     * @return ActiveQueryInterface the newly created [[ActiveQueryInterface]] instance.
     */
    static function find();


    /**
     * Class name of [[ActiveListInterface]] used by this [[ActiveRecordInterface]]
     * @return string
     */
    static function listClass();


    /**
     * Additional column formaters
     * @return array
     */
    static function formats();

    /**
     */
    const VISIBILITY_VISIBLE = true;

    /**
     */
    const VISIBILITY_HIDDEN = false;

    /**
     */
    const VISIBILITY_DEFAULT = null;
}
