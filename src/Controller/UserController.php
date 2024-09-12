<?php

namespace App\Controller;

use App\Entity\Favorite;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\FavoriteRepository;
use App\Repository\PostRepository;
class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(FavoriteRepository $favoriteRepository, PostRepository $postRepository): Response
    {
        $user = $this->getUser();

        $favorites = new Favorite;
        $favorites = $favoriteRepository->findBy(['user_id' => $user->getId()]);

        if ($favorites == true) {
            $posts = $postRepository->getFavorites($user->getId());
        } else {
            $posts = null;
        }
        return $this->render('user/index.html.twig', [
            'posts' => $posts,
            'favotites' => $favorites,
        ]);
    }
}
