<?php

namespace App\Controller;

use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PersistUser implements RequestHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents());

        $name = htmlspecialchars(filter_var($data->name, FILTER_SANITIZE_SPECIAL_CHARS));
        $email = filter_var($data->email, FILTER_VALIDATE_EMAIL);
        $password = filter_var($data->password, FILTER_SANITIZE_SPECIAL_CHARS);

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setPassword($password);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response(201, ['Content-Type' => 'application/json'], json_encode([
            'status' => 'success',
            'message' => 'User created',
        ]));
    }
}
