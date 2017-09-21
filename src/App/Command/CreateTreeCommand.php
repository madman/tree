<?php

namespace App\Command;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Tree\TreeService;
use Tree\Node;

class CreateTreeCommand extends Command {

    protected function configure()
    {
        $this
            ->setName('tree:init')
            ->setDescription("Init tree")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {    
        $app = $this->getSilexApplication();

        $helper = $this->getHelper('question');

        $nameQuestion = new Question('Please enter the name of a root node: ', 'root');
        $titleQuestion = new Question('Please enter the title of a root node: ');
        $wantSetContentQuestion = new ConfirmationQuestion('Want set content? ', false);
        $contentQuestion = new Question('Please enter the content of a root node: ');

        $name = $helper->ask($input, $output, $nameQuestion);
        $title = $helper->ask($input, $output, $titleQuestion);

        if ($helper->ask($input, $output, $wantSetContentQuestion)) {
            $content = $helper->ask($input, $output, $contentQuestion);
        } else {
            $content = '';
        }

        $node = Node::create($name, $title, $content = '');

        $app['service.tree']->create($node);
        

        $output->writeln('Root node was created');
    }    
}