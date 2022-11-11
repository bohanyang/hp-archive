<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\ApiPlatform\SecurityProvider;
use App\Controller\NoContentController;
use Manyou\Mango\Security\User;

#[ApiResource(operations: [
    new Post('/login', name: 'api_login', status: 204, output: false, input: Login::class),
    new Post(
        '/logout',
        name: 'logout',
        status: 204,
        output: false,
        input: false,
        controller: NoContentController::class,
        openapiContext: [
            'summary' => 'Logout',
            'description' => 'Logout and clear the session cookie',
            'requestBody' => ['content' => ['application/json' => ['schema' => ['type' => 'object', 'nullable' => true]]]],
        ],
    ),
    new Get('/user', provider: SecurityProvider::class, output: User::class),
])]
class Session
{
}
