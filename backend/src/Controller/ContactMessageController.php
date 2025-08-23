<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ContactMessageController extends AbstractController
{
    #[Route('/api/my-files', name: 'my_files', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function myFiles(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $messages = $em->getRepository(ContactMessage::class)->findBy(['user' => $user], ['createdAt' => 'DESC']);

        return $this->json(array_map(fn(ContactMessage $m) => [
            'id' => $m->getId(),
            'subject' => $m->getSubject(),
            'message' => $m->getMessage(),
            'attachment' => $m->getAttachmentFilename(),
            'createdAt' => $m->getCreatedAt()->format('Y-m-d H:i:s'),
        ], $messages));
    }

    #[Route('/api/my-files/{id}', name: 'delete_file', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deleteFile(int $id, EntityManagerInterface $em): JsonResponse
    {
        $file = $em->getRepository(ContactMessage::class)->find($id);

        if (!$file || $file->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Not found or unauthorized'], 404);
        }

        $em->remove($file);
        $em->flush();

        return $this->json(['success' => true]);
    }

}
