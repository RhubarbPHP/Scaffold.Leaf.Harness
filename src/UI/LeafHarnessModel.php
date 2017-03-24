<?php

namespace Rhubarb\Leaf\Harness\UI;

use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Leaf\Leaves\LeafModel;

class LeafHarnessModel extends LeafModel
{
    public $leafClass;
    
    /**
     * @var Leaf
     */
    public $leafUnderTest;

    /**
     * @var PropertyDetail[]
     */
    public $constructorProperties = [];

    /**
     * @var PropertyDetail[]
     */
    public $modelProperties = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function getModelValue($parameterName)
    {
        $name = "model_".$parameterName;

        if (isset($this->$name)) {
            return $this->$name;
        } else {
            return null;
        }
    }

    public function setBoundValue($propertyName, $value, $index = null)
    {
        if (stripos($propertyName,"model_") === 0){
            $name = str_replace("model_", "", $propertyName);
            $model = $this->leafUnderTest->getModelForTesting();
            $model->$name = $value;
        }

        parent::setBoundValue($propertyName, $value, $index);
    }

    public function getConstructorParameterValue($parameterName)
    {
        $name = "const_".$parameterName;

        if (isset($this->$name)) {
            return $this->$name;
        } else {
            return null;
        }
    }
}