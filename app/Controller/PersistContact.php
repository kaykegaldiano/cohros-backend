<?php

namespace App\Controller;

use App\Model\Contact;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class PersistContact
{
    private EntityRepository $userRepository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    public function handle(): void
    {
        $contactName = 'John';
        $contactId = 1;

        if (!is_null($contactId) && $contactId !== false) {
            $contact = $this->entityManager->find(Contact::class, $contactId);
            $contact->setName($contactName);
        } else {
            $user = $this->userRepository->findOneBy(['email' => 'test@test.com']);
            $contact = new Contact();
            $contact->setName($contactName);
            $contact->setUser($user);
            $user->getContacts()->add($contact);
            $this->entityManager->persist($contact);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        $this->entityManager->flush();
    }
}