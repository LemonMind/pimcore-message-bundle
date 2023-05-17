<?php

declare(strict_types=1);

namespace LemonMind\MessageBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\PimcoreBundleAdminClassicInterface;
use Pimcore\Extension\Bundle\Traits\BundleAdminClassicTrait;

class LemonmindMessageBundle extends AbstractPimcoreBundle implements PimcoreBundleAdminClassicInterface
{
    use BundleAdminClassicTrait;

    public function getJsPaths(): array
    {
        return [
            '/bundles/lemonmindmessage/js/pimcore/allowedData.js',
            '/bundles/lemonmindmessage/js/pimcore/getAjax.js',
            '/bundles/lemonmindmessage/js/pimcore/startup.js',
        ];
    }
}
