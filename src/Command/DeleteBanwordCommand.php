<?php

namespace App\Command;

use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:delete-banword',
    description: 'Supprime tout les messages possédant un mot banni',
)]
class DeleteBanwordCommand extends Command
{
    public function __construct(
        private MessageRepository $messageRepository,
        private EntityManagerInterface $em
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        /* $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
        ; */
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $banWordList = ['Pokemon', 'Digimon', 'Barbie', 'FromSoftSuck', 'UbisoftTheBest', 'BethesdaUnderatted'];
        $count = 0;
        $messageEntities = $this->messageRepository->findAll();
        foreach ($messageEntities as $message) {
            foreach ($banWordList as $banWord) {
                if (str_contains($message->getContent(), $banWord)) {
                    $count++;
                    $message->getCreatedBy()->incrementNbBanWord();
                    $this->em->remove($message);
                }
            }
        }
        $this->em->flush();
        $output->writeln($count . ' messages ont été supprimés.');
        return Command::SUCCESS;
    }
}
