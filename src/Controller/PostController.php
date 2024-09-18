<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Service\FavoriteService;
use App\Service\PostService;
use App\Service\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class PostController extends AbstractController
    {

    private PostService $postService;
    private CommentService $commentService;
    private FavoriteService $favoriteService;
    public function __construct(

        PostService $postService,
        CommentService $commentService,
        FavoriteService $favoriteService,
        EntityManagerInterface $entityManager,
    ) {

        $this->postService = $postService;
        $this->commentService = $commentService;
        $this->favoriteService = $favoriteService;
        $this->entityManager = $entityManager;
        }
    #[Route('/', name: 'post_list', methods: ['GET'])]
    public function index(PostRepository $postRepository, Request $request): Response
        {


        $page = $request->query->getInt('page', 1);
        $sort = $request->query->get('sort', 'latest');
        $limit = $request->query->getInt('limit', 3);
        $data = [
            'limit' => $limit,
            'page' => $page,
            'sort' => $sort
        ];
        $posts = $this->postService->sortPost($data);
        $totalPosts = $postRepository->getTotalCount();
        $totalPages = ceil($totalPosts / $limit);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'sort' => $sort,
        ]);
        }
    #[Route('/post_loadMore', name: 'post_load_more', methods: ['GET'])]
    public function loadMore(Request $request): Response
        {
        $limit = $request->query->getInt('limit', 3);
        $page = $request->query->getInt('page', 1);
        $sort = $request->query->get('sort', 'latest');

        $data = [
            'limit' => $limit,
            'page' => $page,
            'sort' => $sort
        ];
        $posts = $this->postService->sortPost($data);
        return $this->render('post/_posts.html.twig', [
            'posts' => $posts,
        ]);
        }
    #[Route('/post/{id}', name: 'post_show')]
    public function show(Post $post, Request $request, EntityManagerInterface $entityManager): Response
        {
        $ip = $request->getClientIp();
        // set count views to service 
        $this->postService->incrementViewCount($ip, $post->getId());

        $commentId = $request->request->get('comment_id');
        $comment = $commentId ? $entityManager->getRepository(Comment::class)->find($commentId) : new Comment();
        $comment->setPost($post);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->handleComment($comment);
            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
            }

        $comments = $this->commentService->sortComments($post);
        $favorites = $this->favoriteService->isPostFavorited($post);

        return $this->render('post/show.html.twig', [
            'favorites' => $favorites,
            'post' => $post,
            'form' => $form->createView(),
            'comments' => $comments,
            'editingCommentId' => $commentId ? $commentId : '',
        ]);
        }
    #[Route('/post_edit/{id}', name: 'post_edit')]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
        {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('post_list', [], Response::HTTP_SEE_OTHER);
            }

        return $this->render('post/post_edit.html.twig', [
            'post' => $post,
            'form' => $form,
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
    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
        {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
            }

        return $this->redirectToRoute('post_list', [], Response::HTTP_SEE_OTHER);
        }

    }
