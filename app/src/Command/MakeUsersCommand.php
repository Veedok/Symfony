<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/** Команда для наполнения БД Пользователями */
#[AsCommand(
    name: 'app:make:users',
    description: 'Add a short description for your command',
)]
class MakeUsersCommand extends Command
{
    /**
     * Определение зависимостей
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $hasher
    )
    {
        parent::__construct();
    }

    /**
     * Конфигурация
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    /**
     * Создание пользователей
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $admin = new User();
        $admin->setLogin('Veedok');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $admin->setPassword($this->hasher->hashPassword($admin, '123qwerty'));
        $this->entityManager->persist($admin);
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setLogin('User'.$i);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->hasher->hashPassword($user, 'qwerty123'));
            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();

        $io->success('You have a 20 new Users and 1 ADMIN!');

        return Command::SUCCESS;
    }
}
