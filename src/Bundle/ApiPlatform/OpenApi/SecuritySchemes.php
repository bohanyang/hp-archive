<?php

declare(strict_types=1);

namespace App\Bundle\ApiPlatform\OpenApi;

use ArrayObject;
use Manyou\Mango\ApiPlatform\OpenApi\SecuritySchemesProcessor;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

use function array_combine;
use function array_fill;
use function count;

class SecuritySchemes implements SecuritySchemesProcessor
{
    public function __construct(
        #[Autowire('%env(SL_JOSE_BRIDGE_SERVER_NAME)%/oauth/v2/authorize')]
        private string $authorizationUrl,
        #[Autowire('%env(SL_JOSE_BRIDGE_SERVER_NAME)%/oauth/v2/token')]
        private string $tokenUrl,
        #[Autowire('%env(ZITADEL_ORG_ID)%')]
        private string $zitadelOrgId,
    ) {
    }

    public function __invoke(ArrayObject $securitySchemes): ArrayObject
    {
        $scopes = [
            'openid',
            'profile',
            'email',
            "urn:zitadel:iam:org:id:{$this->zitadelOrgId}",
        ];

        $securitySchemes['oauth'] = [
            'type' => 'oauth2',
            'flows' => [
                'authorizationCode' => [
                    'authorizationUrl' => $this->authorizationUrl,
                    'tokenUrl' => $this->tokenUrl,
                    'scopes' => array_combine($scopes, array_fill(0, count($scopes), '')),
                ],
            ],
        ];

        return $securitySchemes;
    }
}
