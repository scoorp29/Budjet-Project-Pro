<?php

namespace App\Command;

use App\Entity\User;
use App\Manager\SubscriptionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'app:create-admin';
    private $subscriptionManager;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, SubscriptionManager $subscriptionManager)
    {
        $this->subscriptionManager = $subscriptionManager;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Add a short description for your command')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('subscription_id', InputArgument::OPTIONAL, 'Subscription id');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();

        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $subscription_id = $input->getArgument('subscription_id');

        $email_status = filter_var($email, FILTER_VALIDATE_EMAIL);

        $io->note(sprintf('Create a Admin for email: %s', $email));

        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);

        if ($subscription_id) {
            $subscription = $this->subscriptionManager->find($subscription_id);

            //Check if subscription ID exist
            if (!$subscription) {
                $io->error(sprintf('Subscription with id %s don\'t exist !', $subscription_id));
                exit();
            }
            $user->setSubscription($subscription);
            $io->success(sprintf('subscription %s aded to admin !', $subscription_id));
        }

        //Check if email valide
        if (!$email_status) {
            $io->error(sprintf('%s is not a valide email !', $email));
            exit();
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('You have created a Admin with email: %s', $email));
    }
}