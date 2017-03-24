<?php

namespace Rhubarb\Leaf\Harness\UI;

use Rhubarb\Leaf\Controls\Common\Buttons\Button;
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

    private $constructorLeaves = [];
    private $modelLeaves = [];

    private $button;

    protected function createSubLeaves()
    {
        $this->constructorLeaves = [];
        $this->modelLeaves = [];

        parent::createSubLeaves();

        if ($this->model->leafClass){
            foreach($this->model->constructorProperties as $property){
                $controlLeaf = $property->makeEditingLeaf();
                $controlLeaf->setName("const_".$controlLeaf->getName());

                $this->constructorLeaves[] = $controlLeaf;
                $this->registerSubLeaf($controlLeaf);
            }

            foreach($this->model->modelProperties as $property){
                $controlLeaf = $property->makeEditingLeaf();
                $controlLeaf->setName("model_".$controlLeaf->getName());

                $this->modelLeaves[] = $controlLeaf;
                $this->registerSubLeaf($controlLeaf);
            }
        }

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

        $this->registerSubLeaf($this->button = new Button("Update", "Update"));
    }


    protected function printViewContent()
    {
        if ($this->model->leafClass){
            ?>
            <div>
                <h3>Constructor Arguments</h3>
                <?php

                $this->layoutItemsWithContainer("",
                    $this->constructorLeaves
                );

                ?>
                <h3>Model Properties</h3>
                <?php

                $this->layoutItemsWithContainer("",
                    $this->modelLeaves
                );

                print $this->button;

                ?>
            </div>
            <div>
            <?php
            if ($this->model->leafUnderTest) {
                print $this->model->leafUnderTest;
            } else {
                print "<p>The leaf can't be constructed yet as constructor arguments are missing.</p>";
            }
            ?>
            </div>
            <?php
        } else {
            print $this->leaves["leafClass"];
        }
    }
}