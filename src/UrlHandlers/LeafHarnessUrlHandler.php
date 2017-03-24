<?php

namespace Rhubarb\Leaf\Harness\UrlHandlers;

use Rhubarb\Crown\UrlHandlers\GreedyUrlHandler;
use Rhubarb\Leaf\Harness\UI\LeafHarness;

class LeafHarnessUrlHandler extends GreedyUrlHandler
{
    public function __construct()
    {
        parent::__construct(function($class){

            if (trim($class) == ""){
                return new LeafHarness();
            }

            $class = "\\".str_replace("/", "\\", $class);
            return new LeafHarness($class);
        }, [], "(.*)");
    }

}