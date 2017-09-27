<?php

namespace App\Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use App\Component\Security\JWTListener;

class ServiceProvider implements ServiceProviderInterface {

    public function register(Container $pimple)
    {
        $pimple['security.jwt.authentication_listener'] = function() use ($pimple) {
            return new JWTListener($pimple['security.token_storage'],
                $pimple['security.authentication_manager'],
                $pimple['security.jwt.encoder'],
                $pimple['security.jwt']['options'],
                'jwt'
            );
        };

        $pimple['api.version'] = '0.0.1';

        $pimple['service.tree.persistence'] = function() use ($pimple) {
            return new \Tree\Persistence\Dbal($pimple['db'], 'myths');
        };

        $pimple['service.tree'] = function() use ($pimple) {
            return new \Tree\TreeService($pimple['service.tree.persistence']);
        };

        $pimple['app.controller.version'] = function() use ($pimple) {
            return new \App\Controller\ApiVersionController($pimple['api.version']);
        };

        $pimple['app.controller.login'] = function() use ($pimple) {
            return new \App\Controller\LoginController($pimple['users'], $pimple['security.encoder_factory'], $pimple['security.jwt.encoder']);
        };

        $pimple['app.controller.getroot'] = function() use ($pimple) {
            return new \App\Controller\GetRootController($pimple['service.tree']);
        };

        $pimple['app.controller.createroot'] = function() use ($pimple) {
            return new \App\Controller\CreateRootController($pimple['service.tree'], $pimple['form.factory']);
        };

        $pimple['app.controller.findbyid'] = function() use ($pimple) {
            return new \App\Controller\FindByIdController($pimple['service.tree']);
        };

        $pimple['app.controller.findbyname'] = function() use ($pimple) {
            return new \App\Controller\FindByNameController($pimple['service.tree']);
        };

        $pimple['app.controller.findbypath'] = function() use ($pimple) {
            return new \App\Controller\FindByPathController($pimple['service.tree']);
        };

        $pimple['app.controller.parent'] = function() use ($pimple) {
            return new \App\Controller\ParentController($pimple['service.tree']);
        };

        $pimple['app.controller.parents'] = function() use ($pimple) {
            return new \App\Controller\ParentsController($pimple['service.tree']);
        };

        $pimple['app.controller.children'] = function() use ($pimple) {
            return new \App\Controller\ChildrenController($pimple['service.tree']);
        };

        $pimple['app.controller.addchildto'] = function() use ($pimple) {
            return new \App\Controller\AddChildToController($pimple['service.tree'], $pimple['form.factory']);
        };

    }
}
