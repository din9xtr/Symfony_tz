<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;

use App\Repository\PostRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

class PostController extends AbstractController
    {
    #[Route('/', name: 'post_list', methods: ['GET'])]
    public function index(PostRepository $postRepository, Request $request): Response
        {
        $limit = 3;
        $pageNumber = $request->query->getInt('page', 1);
        $offset = ($pageNumber - 1) * $limit;

        $sortType = $request->query->get('sort', 'latest');

        if ($sortType === 'popular') {
            $posts = $postRepository->findAllByView($limit, $offset);
            } else {

            $posts = $postRepository->findBy([], ['id' => 'DESC'], $limit, $offset);
            }

        $totalPosts = $postRepository->getTotalCount();
        $totalPages = ceil($totalPosts / $limit);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'current_page' => $pageNumber,
            'total_pages' => $totalPages,
            'sort' => $sortType,
        ]);
        }
    #[Route('/post_loadMore', name: 'post_load_more', methods: ['GET'])]
    public function loadMore(Request $request, PostRepository $postRepository): Response
        {
        $limit = $request->query->getInt('limit', 3);
        $page = $request->query->getInt('page', 1);
        $offset = ($page - 1) * $limit;
        $sort = $request->query->get('sort', 'latest');

        if ($sort === 'popular') {
            $posts = $postRepository->findAllByView($limit, $offset);
            } else {
            $posts = $postRepository->findBy([], ['id' => 'DESC'], $limit, $offset);
            }

        return $this->render('post/_posts.html.twig', [
            'posts' => $posts,
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
