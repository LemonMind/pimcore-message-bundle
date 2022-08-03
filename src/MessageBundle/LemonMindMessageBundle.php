<?php

namespace LemonMind\MessageBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class LemonMindMessageBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    public function getJsPaths()
    {
        return [
            '/bundles/lemonmindmessage/js/pimcore/startup.js'
        ];
    }
}
