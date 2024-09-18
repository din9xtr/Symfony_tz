<?php
namespace App\Service;
use App\Entity\Favorite;
use App\Entity\Post;
use App\Repository\FavoriteRepository;
use Symfony\Bundle\SecurityBundle\Security;
class FavoriteService
    {
    private FavoriteRepository $favoriteRepository;
    private Security $security;

    public function __construct(FavoriteRepository $favoriteRepository, Security $security)
        {
        $this->favoriteRepository = $favoriteRepository;
        $this->security = $security;

        }

    public function isPostFavorited(Post $post): Favorite|null
        {
        $user = $this->security->getUser();
        if ($user != null) {
            $favorites = new Favorite();

            $favorites = $this->favoriteRepository->isFavorite($user->getId(), $post->getId());
            } else {
            // if post isn`t fav or user isn`t auth
            $favorites = null;
            }
        return $favorites;
        }
    }
