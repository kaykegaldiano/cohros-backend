<?php

namespace App\Controller;

use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PersistUser implements RequestHandlerInterface
{
    /** @var EntityRepository<User> */
    private EntityRepository $userRepository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $name = htmlspecialchars(filter_var($request->getParsedBody()['name'], FILTER_SANITIZE_SPECIAL_CHARS));
        $email = filter_var($request->getParsedBody()['email'], FILTER_VALIDATE_EMAIL);
        $password = filter_var($request->getParsedBody()['password'], FILTER_SANITIZE_SPECIAL_CHARS);

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ($user) {
            http_response_code(401);

            return new Response(401, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'User already exists',
            ]));
        }

        $newUser = new User();
        $newUser->setName($name);
        $newUser->setEmail($email);
        $newUser->setPassword($password);
        $this->entityManager->persist($newUser);
        $this->entityManager->flush();

        http_response_code(201);

        return new Response(201, ['Content-Type' => 'application/json'], json_encode([
            'status' => 'success',
            'message' => 'User created',
        ]));
    }
}
