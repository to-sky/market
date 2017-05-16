<?php

namespace MarketBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MarketBundle extends Bundle
{
	public function getParent()
    {
        return 'FOSUserBundle';
    }
}
