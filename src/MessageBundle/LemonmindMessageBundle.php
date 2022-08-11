<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class LemonmindMessageBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/lemonmindmessage/js/pimcore/startup.js',
        ];
    }
}
