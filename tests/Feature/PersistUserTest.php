<?php

use App\Controller\PersistUser;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

it('creates a user and returns a success response', function () {
    // Mock the request body
    $body = json_encode([
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'password' => 'password',
    ]);

    // Mock the StreamInterface to return the JSON body
    $streamMock = Mockery::mock(StreamInterface::class);
    $streamMock->shouldReceive('getContents')->andReturn($body);

    // Mock the ServerRequestInterface to return the StreamInterface mock
    $requestMock = Mockery::mock(ServerRequestInterface::class);
    $requestMock->shouldReceive('getBody')->andReturn($streamMock);

    // Mock the EntityManagerInterface to expect the user persistence
    $entityManagerMock = Mockery::mock(EntityManagerInterface::class);
    $entityManagerMock->shouldReceive('persist')->once()->withArgs(function (User $user) {
        return 'John Doe' === $user->getName() && 'john.doe@example.com' === $user->getEmail();
    });
    $entityManagerMock->shouldReceive('flush')->once();

    // Create an instance of the PersistUser class with the EntityManagerInterface mock
    $persistUser = new PersistUser($entityManagerMock);

    // Call the handle method and assert the response
    $response = $persistUser->handle($requestMock);

    expect($response->getStatusCode())->toBe(201);
    expect($response->getHeaderLine('Content-Type'))->toBe('application/json');
    $responseData = json_decode((string) $response->getBody(), true);
    expect($responseData)->toMatchArray([
        'status' => 'success',
        'message' => 'User created',
    ]);
});
