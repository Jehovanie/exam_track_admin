<?php 

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

final class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function __invoke(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        
        $email = $data['email'] ?? null;
        $plain = $data['password'] ?? null;

        if (!$email || !$plain) {
            return new JsonResponse(['message' => 'email/password requis'], 400);
        }

        $user = (new User())->setEmail($email);
        $user->setPassword($hasher->hashPassword($user, $plain));
        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'created'], 201);
    }
}
