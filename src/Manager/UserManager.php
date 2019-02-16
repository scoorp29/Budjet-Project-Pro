<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\UserRepository;

class UserManager
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function findAll()
    {
        return $this->userRepository->findAll();
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->userRepository->find($id, $lockMode, $lockVersion);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->userRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->userRepository->findOneBy($criteria, $orderBy);
    }

    //Check role send in parametre
    public function verifyUserRole(User $user, $role)
    {
        if ($user->getRoles() === $role) {
            return true;
        }
    }
}