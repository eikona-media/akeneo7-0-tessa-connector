<?php

namespace Eikona\Tessa\ConnectorBundle;

use Eikona\Tessa\ConnectorBundle\DependencyInjection\EikonaTessaConnectorExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EikonaTessaConnectorBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new EikonaTessaConnectorExtension();
        }

        return $this->extension;
    }
}
