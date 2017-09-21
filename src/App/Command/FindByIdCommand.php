<?php

namespace App\Command;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Tree\TreeService;
use Tree\Exception\NotFoundException;
use Ramsey\Uuid\Uuid;

class FindByIdCommand extends Command {

    protected function configure()
    {
        $this
            ->setName('tree:find:byid')
            ->setDescription("Find node by id")
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('id', InputArgument::REQUIRED, 'Id of node'),
                ])
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {    
        $app = $this->getSilexApplication();

        $id = $input->getArgument('id');

        try {
            $node = $app['service.tree']->findById(Uuid::fromString($id));

            $output->writeln('Node: ' . json_encode($node));
        } catch (NotFoundException $e) {

            $output->writeln(sprintf('Node with id "%s" not found', $id));
        }

        
    }    
}