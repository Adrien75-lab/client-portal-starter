<?php
namespace App\Controller;

use App\Entity\ContactMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private RateLimiterFactory $contactPerUser) {}

    #[Route('/api/contact/can-send', methods: ['GET'])]
    public function canSend(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $limiter = $this->contactPerUser->create((string)$user->getUserIdentifier());
        $probe = $limiter->consume(0);

        return $this->json([
            'allowed' => $probe->isAccepted(),
            'retryAfter' => $probe->isAccepted() ? null : $probe->getRetryAfter()?->format(DATE_ATOM)
        ]);
    }

    #[Route('/api/contact', methods: ['POST'])]
    public function create(Request $r, ValidatorInterface $validator, MailerInterface $mailer): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) return $this->json(['error' => 'Unauthorized'], 401);

        $limiter = $this->contactPerUser->create((string)$user->getUserIdentifier());
        $limit = $limiter->consume(1);
        if (!$limit->isAccepted()) {
            return $this->json(['error' => 'Trop de messages. Réessayez plus tard.', 'retryAfter' => $limit->getRetryAfter()?->format(DATE_ATOM)], 429);
        }

        /** @var UploadedFile|null $file */
        $file = $r->files->get('attachment');
        $subject = (string) $r->request->get('subject');
        $message = (string) $r->request->get('message');

        if ($file) {
            $violations = $validator->validate($file, [new Assert\File(maxSize: '2M')]);
            if (count($violations) > 0) return $this->json(['error' => (string)$violations], 422);
        }

        $filename = null;
        if ($file) {
            $filename = uniqid('att_').'.'.$file->guessExtension();
            $file->move($this->getParameter('kernel.project_dir').'/var/uploads', $filename);
        }

        $cm = new ContactMessage();
        $cm->setUser($user); $cm->setSubject($subject); $cm->setMessage($message); $cm->setAttachmentFilename($filename);
        $this->em->persist($cm); $this->em->flush();

        $email = (new Email())
            ->to('support@example.test')
            ->from($user->getUserIdentifier())
            ->subject('[Contact] '.$subject)
            ->text($message);
        if ($filename) {
            $email->attachFromPath(sprintf('%s/var/uploads/%s', dirname(__DIR__, 2), $filename));
        }
        $mailer->send($email);

        return $this->json(['ok' => true, 'id' => $cm->getId()]);
    }
}
