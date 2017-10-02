<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;

class Application extends \Silex\Application {

	public function __construct()
	{
		parent::__construct();

		$app = $this;

        $app['debug'] = false;

        $app->error(function (\Exception $e, Request $request, $code) use ($app) {
            return $app->json(array("error" => $e->getMessage()), $code);
        });

        $app->error(function (\Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException $e, Request $request, $code) use ($app) {
            return $app->json(array("error" => $e->getMessage()), $code);
        });

		$app->register(new \Silex\Provider\DoctrineServiceProvider(), [
            'db.options' => [
                'driver' => 'pdo_mysql',
                'host' => getenv('DATABASE__HOST'),
                'dbname' => getenv('DATABASE__NAME'),
                'port' => getenv('DATABASE__PORT'),
                'user' => getenv('DATABASE__USER'),
                'password' => getenv('DATABASE__PASSWORD'),
                'charset' => 'utf8',
            ]
        ]);

        $app->register(new \Silex\Provider\FormServiceProvider());
        $app->register(new \Silex\Provider\ValidatorServiceProvider());
        $app->register(new \Silex\Provider\LocaleServiceProvider());
        $app->register(new \Silex\Provider\TranslationServiceProvider(), [
            'locale_fallbacks' => ['ua'],
            'translator.domains' => [],
            'translator.messages' => [],
        ]);

        $app->register(new \Knp\Provider\ConsoleServiceProvider(), [
            'console.name' => 'tree',
            'console.version' => '1.0.0',
        ]);        

        $app->register(new \Silex\Provider\ServiceControllerServiceProvider());
        $app->register(new \Silex\Provider\SecurityServiceProvider());

        /* */
        $app->register(new \App\Provider\SecurityProvider(), [
            'security.jwt' => [
                'secret_key' => 'tree_key_tree',
                'life_time'  => 600,
                'options'    => [
                    'username_claim' => 'name', // default name, option specifying claim containing username
                    'header_name' => 'X-Access-Token', // default null, option for usage normal oauth2 header
                    'token_prefix' => 'Tree',
                ]   
            ],
            'security.encoder.bcrypt.cost' => 4,
        ]);
        $app->register(new \Silex\Provider\SecurityJWTServiceProvider());
        $app->register(new \App\Provider\ServiceProvider());

        /* */
        $this->registerControllers();
        $this->registerCommands();
	}

    protected function registerControllers()
    {
        $this->mount('/api/v1', new \App\Provider\ControllerProvider());
    }

    protected function registerCommands()
    {
        // api
        $this['console']->add(new \App\Command\ApiVersionCommand());

        // tree
        $this['console']->add(new \App\Command\CreateTreeCommand());
        $this['console']->add(new \App\Command\GetRootCommand());
        $this['console']->add(new \App\Command\FindByIdCommand());

        // users
        $this['console']->add(new \App\Command\CreateUserCommand());
    }
	
}
