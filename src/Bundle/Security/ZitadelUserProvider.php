<?php

declare(strict_types=1);

namespace App\Bundle\Security;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use function array_unique;
use function str_replace;
use function strtoupper;

class ZitadelUserProvider implements UserProviderInterface
{
    public function __construct(
        #[Autowire('%env(ZITADEL_PROJECT_ID)%')]
        private string $projectId,
        #[Autowire('%env(ZITADEL_ORGANIZATION_ID)%')]
        private string $orgId,
    ) {
    }

    private function extractRolesFromPayload(array $payload): array
    {
        $roles = [];

        foreach ($payload["urn:zitadel:iam:org:project:{$this->projectId}:roles"] ?? [] as $key => $orgs) {
            if (isset($orgs[$this->orgId])) {
                $roles[] = 'ROLE_' . strtoupper(str_replace(['.', '-', ':'], '_', $key));
            }
        }

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        throw new AuthenticationException('Cannot refresh user: not supported.');
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }

    public function loadUserByIdentifier(string $identifier, ?array $payload = null): UserInterface
    {
        if (! $payload) {
            throw new UserNotFoundException('Cannot load user without JWT payload.');
        }

        return new User($identifier, $this->extractRolesFromPayload($payload));
    }
}
