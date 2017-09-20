<?php

namespace App\Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

class ServiceProvider implements ServiceProviderInterface {

	public function register(Container $pimple) 
	{
		$pimple['api.version'] = '0.0.1';

		$pimple['app.controller.version'] = function() use ($pimple) {
            return new \App\Controller\ApiVersionController($pimple['api.version']);
        };
	}
}
