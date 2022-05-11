<?php

namespace App\Command;

use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:change-password-for-all',
    description: 'Add a short description for your command',
)]
class ChangePasswordForAllCommand extends Command
{
    private EntityManager $entityManager;
    private AccountRepository $accountRepository;

    public function __construct(EntityManagerInterface $entityManager, AccountRepository $accountRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->accountRepository = $accountRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('password', InputArgument::OPTIONAL, 'password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $password = $input->getArgument('password');
        $accountEntities = $this->accountRepository->findAll();
        
        foreach($accountEntities as $account){
            $account->setPassword($password);
            $this->entityManager->persist($account);
        }
        
        $this->entityManager->flush();

        $output->writeln('les password sont changer pour '.COUNT($accountEntities).' comptes');
        return command::SUCCESS;
    }
}
