<?php
namespace App\Security;

use App\Entity\LoginEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener(event: LoginSuccessEvent::class)]
class LoginSuccessSubscriber
{
    public function __construct(private EntityManagerInterface $em) {}
    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (!is_object($user)) return;
        if (method_exists($user, 'setLastLoginAt')) {
            $user->setLastLoginAt(new \DateTimeImmutable());
        }
        $this->em->persist(new LoginEvent($user));
        $this->em->flush();
    }
}
