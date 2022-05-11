<?php

namespace App\Command;

use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:change-all-password',
    description: 'Add a short description for your command',
)]
class ChangeAllPasswordCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private AccountRepository $accountRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('password', InputArgument::REQUIRED, 'new password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $password = $input->getArgument('password');
        $accountEntities = $this->accountRepository->findAll();
        if (count($accountEntities) == 0) {
            $output->writeln('Aucun compte à modifier');
            return Command::FAILURE;
        }
        foreach ($accountEntities as $account) {
            $account->setPassword($password);
            $this->em->persist($account);
        }
        $this->em->flush();
        $output->writeln(count($accountEntities) . ' comptes modifiés.');

        return Command::SUCCESS;
    }
}
