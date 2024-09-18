<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Favorite;
use App\Entity\Post;
use App\Enum\Status;
use App\Service\MailService;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Exception;
class CommentService
    {
    private EntityManagerInterface $entityManager;
    private Security $security;
    private MailService $mailService;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security,
        MailService $mailService,
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->mailService = $mailService;
        }

        public function handleComment(Comment $comment ): void
        {
            if (!$comment->getId()) {
                // For new comments
                $user = $this->security->getUser();
                if (!$user instanceof \App\Entity\User) {
                    throw new Exception('User must be auth');
                }
                $comment->setCommentDate(time());
                $comment->setUser($user);
                $comment->setStatus(Status::pending->value);
            }
        
            if ($comment->getStatus() == Status::approved->value) {
                $emails = $this->entityManager->getRepository(Favorite::class)->getEmailsByPostId($comment->getPost()->getId());
                $this->mailService->sendEmailsToUsers($emails, $comment->getPost()->getTitle(), $comment->getText());
            }
        
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
        }
        
    public function sortComments(Post $post): Collection
        {
        $comments = $post->getComments();
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $comments = $comments->filter(function ($comment) {
                return $comment->getStatus() === Status::approved->value;
                });
            }
        return $comments;

        }
    }

