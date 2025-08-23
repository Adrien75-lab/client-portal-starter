<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class PasswordResetController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/auth/forgot-password', methods: ['POST'])]
    public function request(Request $r): JsonResponse
    {
        // Simplifié: retourne ok sans persister de token (MailHog capture où l'on pourrait envoyer un lien)
        return $this->json(['ok' => true]);
    }

    #[Route('/auth/reset-password', methods: ['POST'])]
    public function reset(Request $r, UserPasswordHasherInterface $hasher): JsonResponse
    {
        // Simplifié pour starter. À compléter si besoin de vrai token.
        return $this->json(['ok' => true]);
    }
}
