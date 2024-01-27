<?php

namespace App\Controller;

use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Firebase\JWT\JWT;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoginUser implements RequestHandlerInterface
{
    /** @var ObjectRepository<User> */
    private ObjectRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents());

        $email = filter_var($data->email, FILTER_VALIDATE_EMAIL);
        $password = filter_var($data->password, FILTER_SANITIZE_SPECIAL_CHARS);

        if (false === $email) {
            return new Response(400, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Invalid email',
            ]));
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (is_null($user) || !$user->checkPasswordIsCorrect($password)) {
            return new Response(400, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Invalid email or password',
            ]));
        }

        $secretKey = $_ENV['JWT_SECRET'];
        $issuedAt = new \DateTimeImmutable();
        $expire = $issuedAt->modify('+1 hour')->getTimestamp();
        $serverName = $request->getServerParams()['HTTP_HOST'];
        $userEmail = $user->getEmail();

        $data = [
            'iat' => $issuedAt->getTimestamp(),
            'iss' => $serverName,
            'nbf' => $issuedAt->getTimestamp(),
            'exp' => $expire,
            'userEmail' => $userEmail,
        ];

        $jwt = JWT::encode($data, $secretKey, 'HS256');

        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'status' => 'success',
            'token' => $jwt,
        ]));
    }
}
