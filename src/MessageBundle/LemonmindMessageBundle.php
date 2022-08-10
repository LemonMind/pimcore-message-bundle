<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class LemonmindMessageBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    public function getJsPaths()
    {
        return [
            '/bundles/lemonmindmessage/js/pimcore/startup.js',
        ];
    }
}
