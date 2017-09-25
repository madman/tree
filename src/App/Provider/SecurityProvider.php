<?php

namespace App\Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;

class SecurityProvider implements ServiceProviderInterface {

    public function register(Container $pimple)
    {
        $pimple['api.version'] = '0.0.1';

        $pimple['security.jwt'] = [
            'secret_key' => 'tree_key_tree',
            'life_time'  => 86400,
            'options'    => [
                'username_claim' => 'name', // default name, option specifying claim containing username
                'header_name' => 'X-Access-Token', // default null, option for usage normal oauth2 header
                'token_prefix' => 'Tree',
            ]
        ];

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
