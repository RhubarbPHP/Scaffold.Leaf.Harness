<?php

namespace Rhubarb\Leaf\Harness\UI;

use Rhubarb\Leaf\Leaves\Leaf;

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

    private function buildProps($propList){
        foreach($propList as $prop){
            
        }
    }
    
    protected function createModel()
    {
        $model = new LeafHarnessModel();
        $model->leafClass = $this->leafClass;

        if ($model->leafClass){
            $class = $model->leafClass;

            if (class_exists($class)) {



                $leaf = new $class();
                return new LeafHarness($leaf);
            }
        }

        return $model;
    }
}