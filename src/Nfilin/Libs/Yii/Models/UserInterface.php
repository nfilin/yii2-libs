<?php

namespace Nfilin\Libs\Yii\Models;

use Nfilin\Libs\InputData;

/**
 * Interface UserInterface
 * @package Nfilin\Libs\Yii\Models
 */
interface UserInterface
{
    /**
     * @param string $access_token
     * @param bool|false $register_if_not_present
     * @return User
     */
    static function authorizeWithFacebook($access_token, $register_if_not_present = false);

    /**
     * @param $user_id
     * @param null $notification_id
     * @return Device
     */
    function generateAccessToken($user_id, $notification_id = null);

    /**
     * @param InputData $input
     * @param bool|false $register_if_not_present
     * @return User
     */
    static function authorizeWithPassword(InputData $input, $register_if_not_present = false);
}