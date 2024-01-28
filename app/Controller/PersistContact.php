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

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $contactId = filter_var($request->getQueryParams()['id'], FILTER_VALIDATE_INT);
        $contactName = htmlspecialchars(filter_var($request->getParsedBody()['name'], FILTER_SANITIZE_SPECIAL_CHARS));
        $contactEmail = filter_var($request->getParsedBody()['email'], FILTER_VALIDATE_EMAIL);
        $contactAddress = filter_var($request->getParsedBody()['address'], FILTER_SANITIZE_SPECIAL_CHARS);
        $contactPhones = filter_var($request->getParsedBody()['phoneNumbers'], FILTER_SANITIZE_SPECIAL_CHARS);
        $phonesArray = explode(',', $contactPhones);

        if (false !== $contactId) {
            $contact = $this->entityManager->find(Contact::class, $contactId);
            $contact->setName($contactName);
            $contact->setEmail($contactEmail);
            $contact->setAddress($contactAddress);
            $this->entityManager->flush();

            return new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'success',
                'message' => 'Contact updated',
            ]));
        }

        $user = $this->userRepository->findOneBy(['email' => 'testuser@test.com']);
        $contact = new Contact();
        $contact->setName($contactName);
        $contact->setEmail($contactEmail);
        $contact->setAddress($contactAddress);
        $contact->setUser($user);
        $user->getContacts()->add($contact);
        foreach ($phonesArray as $ph) {
            if (!empty($ph)) {
                $phone = new Phone();
                $phone->setPhone($ph);
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
