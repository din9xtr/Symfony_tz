<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\S;

class RedirectController extends AbstractController
{
    #[Route('/redirect', name: 'app_redirect')]
    public function redirectBasedOnRole(): RedirectResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Assuming role values are strings like 'admin', 'user', etc.
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles)) {
            return $this->redirectToRoute('app_admin');
        } elseif (in_array('ROLE_EDITOR', $roles)) {
            return $this->redirectToRoute('app_editor');
        } elseif (in_array('ROLE_USER', $roles)) {
            return $this->redirectToRoute('app_user');
        }

        // Default redirect for guests or if role is not recognized
        return $this->redirectToRoute('guest_page');
    }
}