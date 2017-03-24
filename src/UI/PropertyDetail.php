<?php

namespace Rhubarb\Leaf\Harness\UI;

use Rhubarb\Leaf\Controls\Common\Checkbox\Checkbox;
use Rhubarb\Leaf\Controls\Common\Text\NumericTextBox;
use Rhubarb\Leaf\Controls\Common\Text\TextBox;
use Rhubarb\Stem\Models\Model;

class PropertyDetail
{
    public $type;

    public $name;

    public $optional;

    public function makeEditingLeaf()
    {
        switch($this->type){
            case "float":
                return new NumericTextBox($this->name, 2);
                break;
            case "int":
                return new NumericTextBox($this->name, 0);
                break;
            case "bool":
                return new Checkbox($this->name);
                break;
            case "string":
                return new TextBox($this->name);
                break;
            default:

                try {
                  $reflection = new \ReflectionClass($this->type);

                  if ($reflection->isSubclassOf(Model::class)){
                      return new ModelMaker($this->name, $this->type);
                  }

                } catch (\ReflectionException $er){}

                break;
        }

        return new TextBox($this->name);
    }

    public function getValue()
    {

    }
}