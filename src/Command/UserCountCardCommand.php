<?php

namespace App\Command;

use App\Manager\CardManager;
use App\Manager\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCountCardCommand extends Command
{
    protected static $defaultName = 'app:user-count-card';
    private $userManager;
    private $cardManager;

    public function __construct(UserManager $userManager, CardManager $cardManager)
    {
        $this->userManager = $userManager;
        $this->cardManager = $cardManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('email', InputArgument::REQUIRED, 'email description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $user = $this->userManager->findOneBy(['email' => $email]);
        $cards = $this->cardManager->findBy(['user' => $user]);
        $countCards = count($cards);

        if ($email) {
            $io->note(sprintf('Searche cads for user : %s', $email));
            $io->success(sprintf('Found: %s cards', $countCards));
        } else {
            $io->error(sprintf('Erreur ! Email %s do not existe', $email));
        }
    }
}
