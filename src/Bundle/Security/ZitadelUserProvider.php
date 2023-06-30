<?php

declare(strict_types=1);

namespace App\Bundle\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\PayloadAwareUserProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

use function array_unique;
use function str_replace;
use function strtoupper;

class ZitadelUserProvider implements PayloadAwareUserProviderInterface
{
    public function __construct(
        #[Autowire('%env(SL_JOSE_BRIDGE_AUDIENCE)%')]
        private string $projectId,
        #[Autowire('%env(ZITADEL_ORG_ID)%')]
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

        if ($roles === []) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function loadUserByIdentifierAndPayload(string $identifier, array $payload): UserInterface
    {
        return new User($identifier, $this->extractRolesFromPayload($payload));
    }

    public function loadUserByUsernameAndPayload(string $username, array $payload): UserInterface
    {
        return $this->loadUserByIdentifierAndPayload($username, $payload);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        throw new AuthenticationException('`refreshUser` is not supported.');
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        throw new UserNotFoundException('Cannot load user without JWT payload.');
    }
}
