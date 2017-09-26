<?php

namespace App\Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;

class SecurityProvider implements ServiceProviderInterface {

    public function register(Container $pimple)
    {
        $pimple['api.version'] = '0.0.1';

        $pimple['users'] = function () {
            $users = [
                'admin' => [
                    'roles' => ['ROLE_ADMIN'],
                    'password' => '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==', // raw password is foo
                    'enabled' => true,
                ],
            ];

            return new InMemoryUserProvider($users);
        };

        $pimple['security.firewalls'] = [
            'login' => [
                'pattern' => 'api/v1/login',
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
