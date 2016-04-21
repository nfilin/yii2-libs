<?php
namespace Nfilin\Libs\Yii\Models;

/**
 * Interface DeviceInterface
 * @package Nfilin\Libs\Yii\Models
 */
interface DeviceInterface
{
    /**
     * @param $access_token
     * @param $user_id
     * @param $notification_id
     * @return Device
     */
    static function authorizeByToken($access_token, $user_id, $notification_id);

}