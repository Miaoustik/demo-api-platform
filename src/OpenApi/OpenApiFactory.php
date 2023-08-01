<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;
use App\Entity\User;

class OpenApiFactory implements OpenApiFactoryInterface
{

    public function __construct(private readonly OpenApiFactoryInterface $decorated)
    {
    }


    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        /** @var PathItem $path */
        foreach ($openApi->getPaths()->getPaths() as $key => $path) {
            if ($path->getGet() && $path->getGet()->getSummary() === 'hidden') {
                $openApi->getPaths()->addPath($key, $path->withGet(null));
            }
        }

        $securitySchemes = $openApi->getComponents()->getSecuritySchemes();

        $securitySchemes['cookieAuth'] = [
            'type' => 'apiKey',
            "in" => 'cookie',
            "name" => "PHPSESSID"
        ];

        $schemas = $openApi->getComponents()->getSchemas();
        $schemas['Credentials'] = [
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'john@doe.fr'
                ],
                'password' => [
                    'type' => 'string',
                    'example' => '0000'
                ]
            ]
        ];

        $loginPath = new PathItem(
            post: new Operation(
                operationId: 'postApiLogin',
                tags: ['Auth'],
                responses: [
                    '200' => [
                        'description' => "Utilisateur connecté.",
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/User-' . str_replace(':', '.', User::ME)
                                ]
                            ]
                        ]
                    ],
                    '401' => [
                        'description' => "Invalid credentials.",
                        "content" => [
                            'application/json' => [
                                "schema" => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => [
                                            'type' => 'string',
                                            "example" => "Invalid credentials."
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                summary: "Permet de s'authentifier.",
                description: "Permet de s'authentifier.",
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials'
                            ]
                        ]
                    ])
                )
            )
        );
        $openApi->getPaths()->addPath('/api/login', $loginPath);

        $logoutPath = new PathItem(
            post: new Operation(
                operationId: 'postApiLogout',
                tags: ['Auth'],
                responses: [
                    '204' => [
                        'description' => "Utilisateur déconnecté.",
                    ]
                ],
                summary: "Permet de se déconnecter.",
                description: "Permet de se déconnecter."
            )
        );

        $openApi->getPaths()->addPath('/logout', $logoutPath);

        $mePath = $openApi->getPaths()->getPath('/api/me');
        $meOperation = $mePath->getGet();
        $meResponses = $meOperation->getResponses();
        unset($meResponses['404']);
        $openApi->getPaths()->addPath('/api/me', $mePath->withGet($meOperation->withResponses($meResponses)));

        return $openApi;
    }
}