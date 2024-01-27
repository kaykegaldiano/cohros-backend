<?php

namespace App\Controller;

use App\Model\Contact;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
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
        $authorizationHeader = $request->getHeaders()['Authorization'][0];
        $token = str_replace('Bearer ', '', $authorizationHeader);

        try {
            $decodedToken = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));

//            return new Response(201, ['Content-Type' => 'application/json'], json_encode($decodedToken));
        } catch (\Throwable $t) {
            return new Response(401, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => $t->getMessage(),
            ]));
        }

        $data = json_decode($request->getBody()->getContents());

        $contactName = filter_var($data->name, FILTER_SANITIZE_SPECIAL_CHARS);
        $contactEmail = filter_var($data->email, FILTER_VALIDATE_EMAIL);
        $contactAddress = filter_var($data->address, FILTER_SANITIZE_SPECIAL_CHARS);

        //        $contactName = htmlspecialchars(filter_var($request->getParsedBody()['name'], FILTER_SANITIZE_SPECIAL_CHARS));
        $contactId = filter_var($request->getQueryParams()['id'], FILTER_VALIDATE_INT);

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

        $user = $this->userRepository->findOneBy(['email' => $decodedToken->email]);
        $contact = new Contact();
        $contact->setName($contactName);
        $contact->setEmail($contactEmail);
        $contact->setAddress($contactAddress);
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
