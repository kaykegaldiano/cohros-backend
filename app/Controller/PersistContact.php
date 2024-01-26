<?php

namespace App\Controller;

use App\Model\Contact;
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
        $contactName = htmlspecialchars(filter_var($request->getParsedBody()['name'], FILTER_SANITIZE_SPECIAL_CHARS));
        $contactId = filter_var($request->getQueryParams()['id'], FILTER_VALIDATE_INT);

        if (false !== $contactId) {
            $contact = $this->entityManager->find(Contact::class, $contactId);
            $contact->setName($contactName);
            $this->entityManager->flush();

            return new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'success',
                'message' => 'Contact updated',
            ]));
        }

        $user = $this->userRepository->findOneBy(['email' => 'test@test.com']);
        $contact = new Contact();
        $contact->setName($contactName);
        $contact->setUser($user);
        $user->getContacts()->add($contact);
        $this->entityManager->persist($contact);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'status' => 'success',
            'message' => 'Contact created',
        ]));
    }
}
