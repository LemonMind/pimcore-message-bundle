<?php

namespace LemonMind\MessageBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class LemonMindMessageBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/lemonmindmessage/js/pimcore/startup.js'
        ];
    }
}