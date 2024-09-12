<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
class FavoriteController extends AbstractController
{
    private HubInterface $publisher;

    public function __construct(HubInterface $publisher, )
    {
        $this->publisher = $publisher;
    }
    #[Route('/favorite_add', name: 'favorite_add', methods: ['POST'])]
    public function addFavorite(Request $request, EntityManagerInterface $entityManager)
    {
        if ($this->isCsrfTokenValid('add', $request->getPayload()->getString('_token'))) {


            $postId = $request->request->get('post_id');
            $user = $this->getUser();

            $post = $entityManager->getRepository(Post::class)->find($postId);

            $favorite = new Favorite();
            $favorite->setPost($post);
            $favorite->setUser($user);

            $entityManager->persist($favorite);
            $entityManager->flush();
            $update = new Update(
                'admin',
                json_encode([
                    'type' => 'favorite_add',
                    'user' => $user->getlogin(),
                    'post' => $post->getId(),
                    'message' => 'added to favorites',
                ])
            );

            $this->publisher->publish($update);
        }
        return $this->redirect("/post/$postId", );
    }
    #[Route('/favorite_remove', name: 'favorite_remove', methods: ['POST'])]
    public function removeFavorite(Request $request, EntityManagerInterface $entityManager)
    {
        if ($this->isCsrfTokenValid('remove', $request->getPayload()->getString('_token'))) {
            $postId = $request->request->get('post_id');
            $user = $this->getUser();

            $favorite = $entityManager->getRepository(Favorite::class)
                ->findOneBy(['post' => $postId, 'user' => $user]);

            if ($favorite) {
                $entityManager->remove($favorite);
                $entityManager->flush();
            }
        }
        return $this->redirect("/post/$postId", );
    }
}
