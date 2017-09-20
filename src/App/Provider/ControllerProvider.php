<?php

namespace App\Provider;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControllerProvider implements ControllerProviderInterface {

    public function connect(Application $app) {
        /** @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers
            ->get('/', "app.controller.version")
            ->bind('version');

        return $controllers;
    }
}
