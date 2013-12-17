<?php

namespace DCS\OAuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use DCS\OAuthBundle\DependencyInjection\DCSOAuthExtension;

class DCSOAuthBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        // return the right extension instead of "auto-registering" it. Now the
        // alias can be dcs_oauth instead of dcs_o_auth..
        if (null === $this->extension) {
            return new DCSOAuthExtension();
        }

        return $this->extension;
    }
}
