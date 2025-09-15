<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model as OA;
use ArrayObject;

final class OpenApiRegisterDecorator implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $schema = new ArrayObject([
            'type' => 'object',
            'required' => ['email', 'password'],
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'format' => 'email',
                    'example' => 'admin@admin.com',
                ],
                'password' => [
                    'type' => 'string',
                    'minLength' => 4,
                    'example' => 'admin',
                ],
            ],
        ]);

        // --- MediaType + RequestBody
        $mediaType   = new OA\MediaType(schema: $schema);
        $requestBody = new OA\RequestBody(
            description: 'Payload d’inscription',
            required: true,
            // ArrayObject attendu pour la map des content-types
            content: new ArrayObject([
                'application/json' => $mediaType,
            ])
        );

        // --- Responses
        $responses = [
            '201' => new OA\Response(description: 'Utilisateur créé'),
            '404' => new OA\Response(description: 'Données invalides'),
            '409' => new OA\Response(description: 'Email déjà utilisé'),
        ];

        // --- Operation (endpoint public → security vide)
        $operation = new OA\Operation(
            operationId: 'postRegister',
            tags: ['Auth'],
            summary: 'Créer un utilisateur',
            requestBody: $requestBody,
            responses: $responses,
            security: [] // public
        );

        // --- Path item (POST /api/register)
        $pathItem = new OA\PathItem(
            summary: 'Inscription',
            post: $operation
        );

        // Récupère l'objet Paths existant
        $paths = $openApi->getPaths();

        // Ajoute le chemin (addPath ne retourne rien dans cette version)
        $paths->addPath('/api/register', $pathItem);

        // Réinjecte l'objet Paths dans OpenApi
        return $openApi->withPaths(paths: $paths);

    }
}
