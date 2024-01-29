<?php

namespace App\Controller;

use App\Model\Contact;
use App\Model\Phone;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PersistContact implements RequestHandlerInterface
{
    /** @var EntityRepository<User> */
    private EntityRepository $userRepository;

    /** @var EntityRepository<Contact> */
    private EntityRepository $contactRepository;

    /** @var EntityRepository<Phone> */
    private EntityRepository $phoneRepository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->contactRepository = $this->entityManager->getRepository(Contact::class);
        $this->phoneRepository = $this->entityManager->getRepository(Phone::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $contactId = null;
        if (isset($request->getQueryParams()['id'])) {
            $contactId = $request->getQueryParams()['id'] ? filter_var($request->getQueryParams()['id'], FILTER_VALIDATE_INT) : null;
        }
        $contactName = htmlspecialchars(filter_var($request->getParsedBody()['name'], FILTER_SANITIZE_SPECIAL_CHARS));
        $contactEmail = filter_var($request->getParsedBody()['email'], FILTER_VALIDATE_EMAIL);
        $contactAddress = filter_var($request->getParsedBody()['address'], FILTER_SANITIZE_SPECIAL_CHARS);
        $contactPhones = filter_var($request->getParsedBody()['phoneNumbers']);

        $decodedPhones = json_decode($contactPhones, true);

        foreach ($decodedPhones as &$decodedPhone) {
            if (!empty($decodedPhone)) {
                $decodedPhone['number'] = str_replace([' ', '-', '(', ')', ' '], '', $decodedPhone['number']);
            }
        }
        unset($decodedPhone);

        if (false !== $contactId && null !== $contactId) {
            $contact = $this->entityManager->find(Contact::class, $contactId);
            $contact->setName($contactName);
            $contact->setEmail($contactEmail);
            $contact->setAddress($contactAddress);
            foreach ($decodedPhones as $decodedPhone) {
                if (!empty($decodedPhone['number'])) {
                    $phone = null;
                    if (isset($decodedPhone['id'])) {
                        $phone = $this->phoneRepository->findOneBy(['id' => $decodedPhone['id']]);
                        $phone->setPhone($decodedPhone['number']);
                    }
                    if (null === $phone) {
                        $newPhone = new Phone();
                        $newPhone->setPhone($decodedPhone['number']);
                        $newPhone->setContact($contact);
                        $contact->getPhones()->add($newPhone);
                        $this->entityManager->persist($newPhone);
                    }
                }
            }
            $this->entityManager->flush();

            return new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'success',
                'message' => 'Contact updated',
            ]));
        }

        $user = $this->userRepository->findOneBy(['email' => 'testuser@test.com']);
        $contactExists = $this->contactRepository->findOneBy(['email' => $contactEmail]);

        if ($contactExists) {
            return new Response(401, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Contact already exists',
            ]));
        }

        $contact = new Contact();
        $contact->setName($contactName);
        $contact->setEmail($contactEmail);
        $contact->setAddress($contactAddress);
        $contact->setUser($user);
        $user->getContacts()->add($contact);
        foreach ($decodedPhones as $decodedPhone) {
            if (!empty($decodedPhone['number'])) {
                $phone = new Phone();
                $phone->setPhone($decodedPhone['number']);
                $phone->setContact($contact);
                $contact->getPhones()->add($phone);
                $this->entityManager->persist($phone);
            }
        }
        $this->entityManager->persist($contact);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'status' => 'success',
            'message' => 'Contact created',
        ]));
    }
}
