<?php

namespace App\Command;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\User;

class CreateUserCommand extends Command {

    protected function configure()
    {
        $this
            ->setName('users:create')
            ->setDescription("Create new user")
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('username', InputArgument::REQUIRED, 'Name of user'),
                    new InputArgument('password', InputArgument::REQUIRED, 'Plain password'),
                ])
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {    
        $app = $this->getSilexApplication();

        $dbal = $app['db'];
        $encoderFactory = $app['security.encoder_factory'];

        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        try {
            $user = new User($username, $password, ['ROLE_USER']);

            $encoder = $encoderFactory->getEncoder($user);

            $encoded = $encoder->encodePassword($user->getPassword(), $user->getSalt());

            $dbal->insert('users', [
                'id' => Uuid::uuid4()->toString(),
                'username' => $user->getUsername(),
                'password' => $encoded,
                'roles' => implode(',', $user->getRoles()),
            ]);

            $output->writeln('User created');
        } catch (\Exception $e) {

            $output->writeln('User not created. Error: ' . $e->getMessage());
        }
    }
}