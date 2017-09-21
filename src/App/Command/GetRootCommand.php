<?php

namespace App\Command;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tree\TreeService;

class GetRootCommand extends Command {

    protected function configure()
    {
        $this
            ->setName('tree:root')
            ->setDescription("Get root node of tree")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {    
        $app = $this->getSilexApplication();

        $root = $app['service.tree']->getRoot();

        $output->writeln('Root: ' . json_encode($root));
    }    
}