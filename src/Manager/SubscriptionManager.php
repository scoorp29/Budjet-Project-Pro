<?php

namespace App\Manager;

use App\Repository\SubscriptionRepository;

class SubscriptionManager
{
    private $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->subscriptionRepository->find($id, $lockMode, $lockVersion);
    }

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->subscriptionRepository->findOneBy($criteria, $orderBy);
    }

    public function findAll()
    {
        return $this->subscriptionRepository->findAll();
    }
}