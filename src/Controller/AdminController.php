<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route('/admin/change_role/{id}', name: 'admin_change_role', methods: ['POST'])]
    public function changeRole(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $role = $request->request->get('role');

        if (!in_array($role, ['ROLE_USER', 'ROLE_EDITOR', 'ROLE_ADMIN'])) {
            throw $this->createNotFoundException('Invalid role');
        }

        $user->setRole($role);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', "User login: {$user->getLogin()}  id: {$user->getid()} role updated set role : {$user->getRole()}");

        return $this->redirectToRoute('app_admin');
    }

}
