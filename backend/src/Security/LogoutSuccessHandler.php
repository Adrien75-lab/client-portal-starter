<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSuccessHandler
{
    public function onLogoutSuccess(LogoutEvent $event): void
    {
        $event->setResponse(new JsonResponse([
            'message' => 'Déconnecté avec succès'
        ]));
    }
}