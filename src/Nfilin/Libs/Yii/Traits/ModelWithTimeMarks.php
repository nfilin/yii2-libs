<?php

namespace Nfilin\Libs\Yii\Traits;

use Nfilin\Libs\Yii\ActiveRecord as ActiveRecord;
use yii\db\Expression;

/**
 * Adds time formaters to 'created' and 'updated' properties.
 * @property int $created
 * @property int $updated
 * @method refresh()
 */
trait ModelWithTimeMarks
{
    /* @var \Nfilin\Libs\Yii\Controller $this */

    /**
     * See [[ActiveRecord::behaviors()]]
     */
    static function formats()
    {
        return [
            'created' => ActiveRecord::COLUMN_TIMESTAMP,
            'updated' => ActiveRecord::COLUMN_TIMESTAMP,
        ];
    }

    /**
     * See [[\yii\db\ActiveRecord::behaviors()]]
     * @return array
     */
    function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => 'updated',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Refresh record after save
     */
    function afterSave()
    {
        $this->refresh();
    }
}