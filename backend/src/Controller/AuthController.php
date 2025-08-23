<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/auth/register', name: 'auth_register', methods: ['POST'])]
    public function register(Request $r, UserPasswordHasherInterface $hasher, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($r->getContent(), true) ?? [];
        $email = (string)($data['email'] ?? '');
        $password = (string)($data['password'] ?? '');

        $violations = $validator->validate($password, [
            new Assert\Length(min: 8),
            new Assert\Regex(pattern: '/[A-Z]/', message: 'Au moins une majuscule'),
            new Assert\Regex(pattern: '/[0-9]/', message: 'Au moins un chiffre'),
            new Assert\Regex(pattern: '/[^A-Za-z0-9]/', message: 'Au moins un caractère spécial'),
        ]);
        if (count($violations) > 0) {
            return $this->json(['error' => (string)$violations], 422);
        }

        $exists = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($exists) return $this->json(['error' => 'Email déjà utilisé'], 409);

        $u = (new User())->setEmail($email);
        $u->setPassword($hasher->hashPassword($u, $password));
        $this->em->persist($u);
        $this->em->flush();
        return $this->json(['ok' => true]);
    }

    #[Route('/auth/login', name: 'auth_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        $user = $this->getUser();
        return $this->json(['user' => $user?->getUserIdentifier()]);
    }

    #[Route('/auth/me', name: 'auth_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        /** @var \App\Entity\User|null $u */
        $u = $this->getUser();

        if (!$u) {
            return $this->json(['authenticated' => false]);
        }

        return $this->json([
            'authenticated' => true,
            'email' => $u->getUserIdentifier(),
            'filesCount' => $u->getFilesCount(),
        ]);
    }

    #[Route('/auth/logout', name: 'auth_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return new JsonResponse(null, 204); // pas de redirection
    }
}
