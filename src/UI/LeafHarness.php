<?php

namespace Rhubarb\Leaf\Harness\UI;

use Rhubarb\Leaf\Crud\Leaves\ModelBoundModel;
use Rhubarb\Leaf\Exceptions\RequiresViewReconfigurationException;
use Rhubarb\Leaf\Leaves\Controls\ControlModel;
use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Leaf\Leaves\LeafModel;

class LeafHarness extends Leaf
{
    /**
     * @var string
     */
    private $leafClass;

    public function __construct($leafClass = null)
    {
        $this->leafClass = $leafClass;

        parent::__construct();
    }


    /**
    * @var LeafHarnessModel
    */
    protected $model;
    
    protected function getViewClass()
    {
        return LeafHarnessView::class;
    }

    protected function getModelProperties()
    {
        /**
         * @var LeafHarnessModel $model
         */
        $model = $this->model->leafUnderTest->getModelForTesting();

        $reflection = new \ReflectionClass($model);
        $properties = [];

        $baseClasses = [
            LeafModel::class,
            ModelBoundModel::class,
            ControlModel::class
        ];

        foreach($reflection->getProperties() as $parameter){

            if (!$parameter->isPublic()){
                continue;
            }

            $declaringClass = $parameter->getDeclaringClass();

            if (in_array($declaringClass->name, $baseClasses)){
                continue;
            }

            $property = new PropertyDetail();
            $property->name = $parameter->name;
            $doc = $parameter->getDocComment();

            if (preg_match("/@var\\s+(\\S+)/", $doc, $matches)){
                $property->type = $matches[1];
            } else {
                $property->type = "string";
            }

            $properties[] = $property;
        }

        return $properties;
    }

    /**
     * @param $leafClass
     * @return PropertyDetail[]
     */
    protected function getConstructorProperties($leafClass)
    {
        $reflection = new \ReflectionClass($leafClass);
        $constructor = $reflection->getConstructor();
        $properties = [];
        foreach($constructor->getParameters() as $parameter){
            $property = new PropertyDetail();
            $property->name = $parameter->name;
            $property->optional = $parameter->isOptional();
            $property->type = $parameter->hasType() ? (string)$parameter->getType() : "string";
            $properties[] = $property;
        }

        return $properties;
    }

    protected function afterEvents()
    {
        parent::afterEvents();

        if ($this->model->leafClass){
            $class = $this->model->leafClass;

            if (class_exists($class)) {

                // Do we have constructor arguments?
                $properties = $this->model->constructorProperties;

                // Any required properties?
                $missingProps = false;
                foreach($properties as $property) {
                    if (!$property->optional && !$this->model->getConstructorParameterValue($property->name)){
                        $missingProps = true;
                    }
                }

                if (!$missingProps){
                    if (!$this->model->leafUnderTest) {
                        $leaf = $this->makeLeaf($class);
                        $this->model->leafUnderTest = $leaf;

                        // Now we have a leaf we can investigate the properties of it's model.
                        $this->model->modelProperties = $this->getModelProperties();

                        throw new RequiresViewReconfigurationException();
                    }
                }
            }
        }
    }

    protected function makeLeaf($class)
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        $args = [];
        foreach($constructor->getParameters() as $parameter){
             $argValue = $this->model->getConstructorParameterValue($parameter->name);

             if ($argValue || !$parameter->isOptional()) {
                 $args[] = $argValue;
             } else {
                 break;
             }
        }

        $leaf = $reflection->newInstanceArgs($args);

        return $leaf;
    }

    protected function createModel()
    {
        $model = new LeafHarnessModel();
        $model->leafClass = $this->leafClass;

        if ($model->leafClass) {
            $class = $model->leafClass;

            if (class_exists($class)) {

                // Do we have constructor arguments?

                $model->constructorProperties = $this->getConstructorProperties($class);
            }
        }

        return $model;
    }
}