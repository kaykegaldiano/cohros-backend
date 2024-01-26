<?php

namespace App\Controller;

use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ListContacts implements RequestHandlerInterface
{
    /** @var EntityRepository<User> */
    private EntityRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $this->userRepository->findOneBy(['email' => 'test@test.com']);
        $contacts = $user->getContacts();

        return new Response(200, ['Content-Type' => 'application/json'], json_encode($contacts));
    }
}
