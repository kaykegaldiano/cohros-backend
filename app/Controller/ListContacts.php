<?php

namespace App\Controller;

use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class ListContacts
{
    private EntityRepository $userRepository;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    public function handle(): string
    {
        $user = $this->userRepository->findOneBy([['email' => 'test@test.com']]);
        $contacts = $user->getContacts();

        return json_encode($contacts);
    }
}
