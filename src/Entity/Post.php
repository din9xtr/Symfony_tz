<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table(name: "posts")]

class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?string $post_text = null;

    #[ORM\Column(type: 'integer')]
    private ?int $post_date = null;

    #[ORM\Column(length: 255)]
    private ?string $author = null;

    #[ORM\Column]
    private ?int $author_id = null;

    #[ORM\Column]
    private ?int $view_count = 0;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    public function getComments(): Collection
    {
        return $this->comments;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        $this->author_id = $user->getId();
        $this->author = $user->getLogin();
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPostText(): ?string
    {
        return $this->post_text;
    }

    public function setPostText(string $post_text): static
    {
        $this->post_text = $post_text;

        return $this;
    }

    public function getPostDate(): ?int
    {
        return $this->post_date;
    }

    public function setPostDate(): static
    {
        $this->post_date = time();

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getAuthorId(): ?int
    {
        return $this->author_id;
    }

    public function setAuthorId(int $author_id): static
    {
        $this->author_id = $author_id;

        return $this;
    }

    public function getViewCount(): ?int
    {
        return $this->view_count;
    }

    public function setViewCount(int $view_count): static
    {
        $this->view_count = $view_count;

        return $this;
    }
}