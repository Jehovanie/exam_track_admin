<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SecurityController 
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): Response
    {
        // Ne sera pas exécuté : la sécurité intercepte avant.
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
