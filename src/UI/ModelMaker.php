<?php

namespace Rhubarb\Leaf\Harness\UI;

use Rhubarb\Leaf\Crud\Leaves\ModelBoundLeaf;
use Rhubarb\Leaf\Leaves\BindableLeafInterface;
use Rhubarb\Leaf\Leaves\BindableLeafTrait;
use Rhubarb\Leaf\Leaves\Leaf;

class ModelMaker extends ModelBoundLeaf implements BindableLeafInterface
{
    use BindableLeafTrait;

    /**
    * @var ModelMakerModel
    */
    protected $model;

    public function __construct($name, $modelClass)
    {
        parent::__construct(new $modelClass());

        $this->setName($name);
    }

    protected function getViewClass()
    {
        return ModelMakerView::class;
    }

    protected function onModelCreated()
    {
        parent::onModelCreated();

        $schema = $this->model->restModel->getSchema();
        $columns = $schema->getColumns();
        $theColumns = [];

        foreach($columns as $column){
            if ($column->columnName != $schema->uniqueIdentifierColumnName){
                $theColumns[] = $column;
            }
        }

        $this->model->columns = $theColumns;
    }

    protected function createModel()
    {
        $model = new ModelMakerModel();
        $model->childControlValueChangedEvent->attachHandler(function(){
            $this->getBindingValueChangedEvent()->raise();
        });

        return $model;
    }

    public function getValue()
    {
        return $this->model->restModel;
    }
}