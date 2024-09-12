<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PostRepository;


class EditorController extends AbstractController
{
    #[Route('/editor', name: 'app_editor')]
    public function index(PostRepository $postRepository): Response
    {
        $user = $this->getUser();
        $posts = $postRepository->findBy(['author' => $user->getLogin()]);
        if (!$posts) {
            $posts = null;
        }
        return $this->render('editor/index.html.twig', [
            'posts' => $posts,
        ]);
    }
}
