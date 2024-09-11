<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\PostType;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Enum\Status;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

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
    public function show(Post $post, Request $request, EntityManagerInterface $entityManager,PostRepository $postRepository): Response
    {
        /// ip check
        $ip = $request->getClientIp();
        $postRepository->incrementViewCount($ip, $post->getId());

        //cooment edit
        $comment_id = $request->request->get('comment_id');
        if ($comment_id) {
            $comment = $entityManager->getRepository(Comment::class)->find($comment_id);
        } else {
            
            $comment = new Comment();
            $comment->setPost($post);
        }
        
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            if (!$comment_id) {
                $comment->setCommentDate(time());
                $comment->setUser($this->getUser());
                $comment->setStatus(Status::pending->value);
            }

            $entityManager->persist($comment);
            $entityManager->flush();
        }

        $comments = $post->getComments();
        if (!$this->isGranted('ROLE_ADMIN')) {
            $comments = $comments->filter(function ($comment) {
                return $comment->getStatus() === Status::approved->value;
            });
        }
        return $this->render('post/show.html.twig', [

            'post' => $post,
            'form' => $form->createView(),
            'comments' => $comments,
            'editingCommentId' => $comment_id,
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
