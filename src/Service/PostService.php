<?php
namespace App\Service;
use App\Repository\PostRepository;

class PostService
    {
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
        {
        $this->postRepository = $postRepository;
        }

    public function incrementViewCount(string $ip, int $postId): void
        {
        $this->postRepository->incrementViewCount($ip, $postId);
        }
    public function sortPost(array $data)
        {

        $limit = $data['limit'];
        $page = $data['page'];
        $sort = $data['sort'];
        $offset = ($page - 1) * $limit;


        if ($sort === 'popular') {
            $posts = $this->postRepository->findAllByView($limit, $offset);
            } else {
            $posts = $this->postRepository->findBy([], ['id' => 'DESC'], $limit, $offset);
            }
        return $posts;
        }
    }