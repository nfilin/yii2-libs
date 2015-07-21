<?php

namespace Nfilin\Libs\Yii;

use Yii;
use yii\web\Controller as YiiController;
use yii\db\Query;
use Nfilin\Libs\InputData;

/**
* @inheritdoc
*/
abstract class Controller extends YiiController {

    /**
     * @var InputData Request data
     */
	protected $input;

    /**
     * @var string Wrapper class for input data
     */
    protected $inputWrapperClass = '\\zeus\\InputData';

    /**
     * @inheritdoc
     */
	public function beforeAction($action) {
		/*if (!parent::beforeAction($action)) {
            return false;
        }*/

    	$request = Yii::$app->request;
    	$this->input = new $this->inputWrapperClass();

        error_log(print_r([
            $request->getQueryParams(),
            $request->getCookies(),
            $request->getBodyParams(),
            /*$request*/
            ],true));
    	$this->input->mergeWith($request->getQueryParams());
    	$this->input->mergeWith($request->getCookies());
    	$this->input->mergeWith($request->getBodyParams());    	

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterAction($action, $result){
    	//print_r($result);
    	return $result;
    }
}