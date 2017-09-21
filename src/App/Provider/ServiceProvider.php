<?php

namespace App\Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

class ServiceProvider implements ServiceProviderInterface {

    public function register(Container $pimple)
    {
        $pimple['api.version'] = '0.0.1';

        $pimple['service.tree.persistence'] = function() use ($pimple) {
            return new \Tree\Persistence\Dbal($pimple['db']);
        };

        $pimple['service.tree'] = function() use ($pimple) {
            return new \Tree\TreeService($pimple['service.tree.persistence']);
        };

        $pimple['app.controller.version'] = function() use ($pimple) {
            return new \App\Controller\ApiVersionController($pimple['api.version']);
        };

        $pimple['app.controller.getroot'] = function() use ($pimple) {
            return new \App\Controller\GetRootController($pimple['service.tree']);
        };

    }
}
