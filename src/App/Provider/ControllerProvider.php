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

        $controllers
            ->get('/root', "app.controller.getroot")
            ->bind('getroot');

        $controllers
            ->post('/create', "app.controller.createroot")
            ->bind('createroot');

        $controllers
            ->get('/findbyid/{id}', "app.controller.findbyid")
            ->bind('findbyid');

        $controllers
            ->get('/findbyname/{name}', "app.controller.findbyname")
            //->assert('name', '/^[a-z0-9-]+$/')
            ->bind('findbyname');

        $controllers
            ->get('/findbypath/{path}', "app.controller.findbypath")
            ->assert('path', '.+')
            ->bind('findbypath');

        $controllers
            ->get('/parent/{id}', "app.controller.parent")
            ->bind('parent');

        $controllers
            ->get('/parents/{id}', "app.controller.parents")
            ->bind('parents');

        $controllers
            ->get('/children/{id}', "app.controller.children")
            ->bind('children');

        $controllers
            ->post('/addchildto/{id}', "app.controller.addchildto")
            ->bind('addchildto');

        return $controllers;
    }
}
