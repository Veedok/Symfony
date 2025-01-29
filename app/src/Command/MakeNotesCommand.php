<?php

namespace App\Command;

use App\Entity\Note;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/** Команда для наполнения БД записями пользователей */
#[AsCommand(
    name: 'app:make:notes',
    description: 'Add a short description for your command',
)]
class MakeNotesCommand extends Command
{
    /**
     * Определение зависимостей
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $users
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository         $users,
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
     * Метод для создания записей в БД
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $qb = $this->users->createQueryBuilder('u')->where("u.roles NOT LIKE '%ROLE_ADMIN%'");
        $query = $qb->getQuery();
        $users = $query->execute();
        for ($i = 0; $i < 100; $i++) {
            $user = new Note();
            $user->setBody('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat');
            $user->setCreateDt(Clock::get()->now());
            $user->setUserId($users[array_rand($users)]);
            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
