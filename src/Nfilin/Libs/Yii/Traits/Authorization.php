<?php
namespace Nfilin\Libs\Yii\Traits;

use Nfilin\Libs\InputData;
use Nfilin\Libs\Yii\Models\User;
use Nfilin\Libs\Yii\Models\Device;
use yii\web;
use yii\web\UnauthorizedHttpException;
use yii\web\BadRequestHttpException;

/**
 * Class AuthorizationTrait
 * @package Nfilin\Libs\Yii\Traits
 * Adds user and device objecst and method to initialize them
 * Should be used in controller
 * @property InputData $input
 */
trait Authorization
{
    /**
     * @var User Request user object
     */
    protected $user;

    /**
     * @var Device Request device object
     */
    protected $device;

    /**
     * Initializes user and device objects
     * @return $this
     * @throws UnauthorizedHttpException If access toke is invalid
     * @throws BadRequestHttpException If no access tokens or device id set.
     */
    protected function initUser()
    {
        if ($this->input->validate(['fb_access_token', 'udid'])) {
            $this->user = User::authorizeWithFacebook($this->input->fb_access_token, true);
            if (empty($this->user))
                throw new UnauthorizedHttpException('Unauthorized user', 403);
            $this->device = $this->user->generateAccessToken($this->input->udid, $this->input->notification_id);
        } elseif ($this->input->validate(['access_token', 'udid'])) {
            $this->device = Device::authorizeByToken($this->input->access_token, $this->input->udid, $this->input->notification_id);
            if (empty($this->device))
                throw new UnauthorizedHttpException('Unauthorized user', 403);
            $this->user = $this->device->owner;
        } elseif (
            (
                $this->input->validate(['email'])
                || $this->input->validate(['user_name'])
            )
            && $this->input->validate(['password', 'udid'])
        ) {
            $this->user = User::authorizeWithPassword($this->input, true);
            if (empty($this->user))
                throw new UnauthorizedHttpException('Unauthorized user', 403);
            $this->device = $this->user->generateAccessToken($this->input->udid, $this->input->notification_id);
        } else
            throw new BadRequestHttpException('Authorization parameters not set :' . json_encode($this->input), 403);
        return $this;
    }
}