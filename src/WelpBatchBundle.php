<?php

namespace Welp\BatchBundle;

use Welp\BatchBundle\DependencyInjection\WelpBatchExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class WelpBatchBundle extends Bundle
{
    /**
     * Get container extension
     *
     * @return ExtensionInterface
     */
    public function getContainerExtension()
    {
        if (!$this->extension instanceof ExtensionInterface) {
            $this->extension = new WelpBatchExtension();
        }

        return $this->extension;
    }
}
