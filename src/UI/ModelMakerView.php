<?php

namespace Rhubarb\Leaf\Harness\UI;

use Rhubarb\Leaf\Views\View;

class ModelMakerView extends View
{
    /**
    * @var ModelMakerModel
    */
    protected $model;

    protected function createSubLeaves()
    {
        parent::createSubLeaves();

        foreach($this->model->columns as $column){
            $this->registerSubLeaf($column->columnName);
        }
    }

    protected function printViewContent()
    {
        $fields = [];

        foreach($this->model->columns as $column){
            $fields[] = $column->columnName;
        }

        $this->layoutItemsWithContainer($this->model->leafName, $fields);
    }
}