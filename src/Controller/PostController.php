<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Form\Post1Type;

use App\Repository\PostRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

class PostController extends AbstractController
{
    #[Route('/', name: 'post_list')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();

        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }
    #[Route('/post/{id}', name: 'post_show')]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }
    #[Route('/post_create', name: 'post_create', methods: ['GET', 'POST'])]
    public function create(Request $request, PostRepository $postRepository): Response
    {
        $post = new Post();
        $user = $this->getUser();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post->setUser($user);
            $post->setPostDate();
            $postRepository->save($post, true);

            return $this->redirectToRoute('post_list');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form,
            'post' => $post,
        ]);
    }

}
