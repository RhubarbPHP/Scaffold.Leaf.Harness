<?php

namespace Rhubarb\Leaf\Harness\UI;

use Rhubarb\Crown\Events\Event;
use Rhubarb\Leaf\Crud\Leaves\ModelBoundModel;
use Rhubarb\Stem\Schema\Columns\Column;

class ModelMakerModel extends ModelBoundModel
{
    /**
     * @var Column[]
     */
    public $columns;

    /**
     * @var Event
     */
    public $childControlValueChangedEvent;

    public function __construct()
    {
        parent::__construct();

        $this->childControlValueChangedEvent = new Event();
    }

    public function setBoundValue($propertyName, $value, $index = null)
    {
        parent::setBoundValue($propertyName, $value, $index);

        $this->childControlValueChangedEvent->raise($propertyName, $value);
    }
}