<?php

namespace Rhubarb\Leaf\Harness\UI;

use Rhubarb\Leaf\Controls\Common\SelectionControls\DropDown\DropDown;
use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Leaf\Leaves\LeafDeploymentPackage;
use Rhubarb\Leaf\Views\View;

class LeafHarnessView extends View
{
    /**
    * @var LeafHarnessModel
    */
    protected $model;

    private function scanForLeaves($path)
    {
        $classes = [];

        $d = scandir($path);
        foreach($d as $dir){
            if ($dir[0] == "."){
                continue;
            }

            if (preg_match("/\\.php$/", $dir)){
                // Get the namespace
                $code = file_get_contents($path."/".$dir);
                if (preg_match("/namespace\\s+([^;]+)/i", $code, $match)){
                    $namespace = $match[1];

                    if (preg_match("/class\\s+(\\S+)/i", $code, $match)){
                        $class = $match[1];
                        $class = $namespace."\\".$class;

                        try {
                            $reflection = new \ReflectionClass($class);

                            if ($reflection->isSubclassOf(Leaf::class)) {
                                $classes[] = $class;
                            }
                        } catch (\ReflectionException $er){}
                    }
                }
            }

            if (is_dir($path."/".$dir)){
                $classes = array_merge($classes, $this->scanForLeaves($path."/".$dir));
            }
        }

        return $classes;
    }

    public function getDeploymentPackage()
    {
        return new LeafDeploymentPackage(__DIR__."/LeafHarnessViewBridge.js");
    }

    protected function getViewBridgeName()
    {
        return "LeafHarnessViewBridge";
    }

    protected function createSubLeaves()
    {
        parent::createSubLeaves();

        if ($this->model->leafUnderTest) {
            $this->registerSubLeaf($this->model->leafUnderTest);
        } else {
            // Create a drop down with all the leaves...
            $selection = new DropDown("leafClass");
            $leaves = $this->scanForLeaves(APPLICATION_ROOT_DIR."/src/");
            array_splice($leaves,0,0,[[0,"Select a leaf"]]);
            $selection->setSelectionItems(
                $leaves
            );

            $this->registerSubLeaf($selection);
        }
    }


    protected function printViewContent()
    {
        if ($this->model->leafClass){
            ?>
            <div>
                <h3>Constructor Arguments</h3>
            </div>
            <div>
            <?php
            if ($this->model->leafUnderTest) {
                print $this->model->leafUnderTest;
            }
            ?>
            </div>
            <?php
        } else {
            print $this->leaves["leafClass"];
        }
    }
}