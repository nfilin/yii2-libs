<?php

namespace Nfilin\Libs\Yii;

use Yii;
use yii\web\Controller as YiiController;
use yii\db\Query;
use Nfilin\Libs\InputData;

/**
 * @inheritdoc
 */
abstract class Controller extends YiiController
{
    /**
     * @var InputData Request data
     */
    protected $input;

    /**
     * @var string Wrapper class for input data
     */
    protected $inputWrapperClass = '\\Nfilin\\Libs\\InputData';

    /**
     * @var double Request start timemark
     */
    protected $start_timemark;

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        /*if (!parent::beforeAction((array)$action)) {
            return false;
        }*/
        $this->start_timemark = microtime(true);

        $request = Yii::$app->request;
        $this->input = new $this->inputWrapperClass();

        $this->input->mergeWith($request->getQueryParams());
        $this->input->mergeWith($request->getCookies());
        $this->input->mergeWith($request->getBodyParams());

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterAction($action, $result)
    {
        //print_r($result);
        Yii::$app->response->headers
            ->set('X-Zeus-Action-Started-At', $this->start_timemark)
            ->set('X-Zeus-Spent-Time', microtime(true) - $this->start_timemark);
        return $result;
    }
}