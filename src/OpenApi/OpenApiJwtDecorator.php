<?php 

// src/OpenApi/OpenApiJwtDecorator.php
namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model as OA;
use ArrayObject;

final class OpenApiJwtDecorator implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi    = ($this->decorated)($context);
        $components = $openApi->getComponents();

        // 1) Security Scheme: HTTP Bearer (JWT)
        $schemes = $components->getSecuritySchemes() ?? new ArrayObject();
        $schemes['bearerAuth'] = new OA\SecurityScheme(
            type: 'http',
            scheme: 'bearer',
            bearerFormat: 'JWT'
        );
        $components = $components->withSecuritySchemes($schemes);

        // 2) Global security: tout est protégé par défaut
        $openApi = $openApi->withSecurity([
            new OA\SecurityRequirement(['bearerAuth' => []]),
        ]);

        // 3) Rendre login/register publics dans la doc (optionnel)
        $paths = $openApi->getPaths();
        foreach (['/api/login', '/api/register'] as $publicPath) {
            if ($item = $paths->getPath($publicPath)) {
                if ($post = $item->getPost()) {
                    $paths->addPath($publicPath, $item->withPost($post->withSecurity([])));
                }
            }
        }

        return $openApi->withComponents($components)->withPaths($paths);
    }
}
