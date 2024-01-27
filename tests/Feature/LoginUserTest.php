<?php

use App\Controller\LoginUser;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

it('logins a user and returns a success response', function () {
    // Mock the request body
    $body = json_encode([
        'email' => 'john.doe@example.com',
        'password' => 'password',
    ]);

    // Mock the StreamInterface to return the JSON body
    $streamMock = Mockery::mock(StreamInterface::class);
    $streamMock->shouldReceive('getContents')->andReturn($body);

    // Mock the ServerRequestInterface to return the StreamInterface mock
    $requestMock = Mockery::mock(ServerRequestInterface::class);
    $requestMock->shouldReceive('getBody')->andReturn($streamMock);
    $requestMock->shouldReceive('getServerParams')->andReturn(['HTTP_HOST' => 'example.com']);

    // Mock the User object
    $userMock = Mockery::mock(User::class);
    $userMock->shouldReceive('getEmail')->andReturn('john.doe@example.com');
    $userMock->shouldReceive('checkPasswordIsCorrect')->with('password')->andReturn(true);

    // Mock the ObjectRepository to return a user
    $userRepositoryMock = Mockery::mock(ObjectRepository::class);
    $userRepositoryMock->shouldReceive('findOneBy')
        ->once()
        ->with(['email' => 'john.doe@example.com'])
        ->andReturn($userMock)
    ;

    // Mock the EntityManagerInterface to expect the user repository
    $entityManagerMock = Mockery::mock(EntityManagerInterface::class);
    $entityManagerMock->shouldReceive('getRepository')
        ->with(User::class)
        ->andReturn($userRepositoryMock)
    ;

    $jwtMock = Mockery::mock('alias:Firebase\JWT\JWT');
    $jwtMock->shouldReceive('encode')
        ->once()
        ->withArgs(function ($data, $secretKey, $alg) {
            return true;
        })
        ->andReturn('mocked_jwt_token')
    ;

    // Create an instance of the LoginUser class with the EntityManagerInterface mock
    $loginUser = new LoginUser($entityManagerMock);

    // Call the handle method and assert the response
    $response = $loginUser->handle($requestMock);

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getHeaderLine('Content-Type'))
        ->toBe('application/json')
    ;
    $responseData = json_decode((string) $response->getBody(), true);
    expect($responseData)->toMatchArray([
        'status' => 'success',
        'token' => 'mocked_jwt_token',
    ]);
});

afterEach(function () {
    Mockery::close();
});
