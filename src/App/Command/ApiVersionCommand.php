<?php

namespace App\Command;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApiVersionCommand extends Command {

	protected function configure()
    {
        $this
	        ->setName('api:version')
	        ->setDescription("Show TreeAPI version")
	    ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {    
    	 $app = $this->getSilexApplication();

    	 $output->writeln($app['api.version']);
    }	 
}