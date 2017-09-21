<?php

namespace App;

class Application extends \Silex\Application {

	public function __construct()
	{
		parent::__construct();

		$app = $this;

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

        /* */
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
        $this['console']->add(new \App\Command\ApiVersionCommand());
    }
	
}
