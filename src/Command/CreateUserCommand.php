<?php

declare(strict_types=1);

namespace App\Command;

use App\Bundle\Security\Doctrine\TableProvider\UsersTable;
use App\Bundle\Security\User;
use Manyou\Mango\Doctrine\SchemaProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Ulid;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create user',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private SchemaProvider $schema,
        private UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $user = new User($id = new Ulid(), $username, null);

        $this->schema->createQuery()->insert(UsersTable::NAME, [
            'id' => $id,
            'username' => $username,
            'password' => $this->hasher->hashPassword($user, $password),
        ])->executeStatement();

        return Command::SUCCESS;
    }
}
