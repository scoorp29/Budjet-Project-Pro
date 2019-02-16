<?php

namespace App\Manager;

use App\Repository\CardRepository;

class CardManager
{
    private $cardRepository;

    public function __construct(CardRepository $cardRepository)
    {
        $this->cardRepository = $cardRepository;
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->cardRepository->find($id, $lockMode, $lockVersion);
    }

    public function findAll()
    {
        return $this->cardRepository->findAll();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->cardRepository->findBy($criteria, $orderBy, $limit, $offset);
    }


    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->cardRepository->findOneBy($criteria, $orderBy);
    }

}