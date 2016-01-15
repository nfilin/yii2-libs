<?php

namespace Nfilin\Libs\Yii;

use yii\base\InvalidParamException;
use yii\web\RequestParserInterface;
use yii\web\BadRequestHttpException;
use StdClass;
use Nfilin\Libs\ObjectTools;

/**
 * Class MultipartParser
 * @package Nfilin\Libs\Yii
 */
class MultipartParser implements RequestParserInterface
{
    /**
     * @var boolean whether to return objects in terms of associative arrays.
     */
    public $asArray = true;
    /**
     * @var boolean whether to throw a [[BadRequestHttpException]] if the body is invalid json
     */
    public $throwException = true;
    /**
     * @var array|object Parsed data
     */
    protected $data = [];
    /**
     * @var array|object `$_POST`
     */
    protected $post = [];
    /**
     * @var array|object `$_FILES`
     */
    protected $files = [];

    /**
     * @inheritdoc
     */
    public function parse($rawBody, $contentType)
    {
        if ($this->asArray) {
            $this->data = [];
            $this->post = $_POST;
            $this->files = ObjectTools::object2array(self::parseFiles());
        } else {
            $this->data = new StdClass;
            $this->post = ObjectTools::array2object($_POST);
            $this->files = self::parseFiles();
        }
        $this->mergeData($this->post)
            ->mergeData($this->files);
        return $this->data;
    }

    /**
     * Add data to parser storage
     * @param array|object $data
     * @return MultipartParser
     */
    protected function mergeData($data = [])
    {
        foreach ($data as $key => $value)
            $this->setParam($key, $value);
        return $this;
    }

    /**
     * Add single parameter value to parser storage
     * @param string $key
     * @param mixed $value
     * @return MultipartParser
     */
    protected function setParam($key, $value)
    {
        if ($this->asArray)
            $this->data[$key] = $value;
        else
            $this->data->{$key} = $value;
        return $this;
    }


    /**
     * Fixes `$_FILES` into hierarchy
     * @return StdClass
     */
    static private function parseFiles()
    {
        $files = new StdClass;
        foreach ($_FILES as $key => $file)
            foreach ($file as $param => $value) {
                self::parseParam($value, $key, $param, $files);
            }
        return $files;
    }

    /**
     * Parses single param of `$_FILES`
     * @param mixed $value Value which should be parsed
     * @param string $key Key to be stored in parent object
     * @param string $param Name of parameter which is currently parsed
     * @param StdClass $parent Parent object
     */
    static private function parseParam($value, $key, $param, StdClass $parent)
    {
        if (empty($value) || is_scalar($value)) {
            if (empty($parent->{$key}))
                $parent->{$key} = new File;
            $parent->{$key}->{$param} = $value;
        } else
            foreach ($value as $subkey => $subvalue) {
                if (empty($parent->{$key}))
                    $parent->{$key} = new StdClass;
                self::parseParam($subvalue, $subkey, $param, $parent->{$key});
            }

    }
}