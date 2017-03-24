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

    public function __construct()
    {
        parent::__construct();
    }
}