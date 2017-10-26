<?php

namespace App\Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use App\Component\UserProvider;

class SecurityProvider implements ServiceProviderInterface {

    public function register(Container $pimple)
    {
        $pimple['users'] = function () use ($pimple) {
            return new UserProvider($pimple['db']);
        };

        $pimple['security.firewalls'] = [
            'login' => [
                'pattern' => 'api/v1/login',
                'anonymous' => true,
            ],
            'version' => [
                'pattern' => 'api/v1/',
                'anonymous' => true,
            ],

            'secured' => [
                'pattern' => '^.*$',
                'logout' => [
                    'logout_path' => '/logout'
                ],
                'users' => $pimple['users'],
                'jwt' => [
                    'use_forward' => true,
                    'require_previous_session' => false,
                    'stateless' => true,
                ]
            ],
        ];

    }
}
