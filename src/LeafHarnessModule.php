<?php

namespace Rhubarb\Leaf\Harness;

use Rhubarb\Crown\Module;
use Rhubarb\Leaf\Harness\UrlHandlers\LeafHarnessUrlHandler;

class LeafHarnessModule extends Module
{
    protected function registerUrlHandlers()
    {
        $handler = new LeafHarnessUrlHandler();

        // We keep this handler above login validation etc. to make sure developers can test
        // without having to login etc.
        $handler->setPriority(100);

        $this->addUrlHandlers(
            [
                "/harness/" => $handler
            ]
        );

        parent::registerUrlHandlers();
    }
}