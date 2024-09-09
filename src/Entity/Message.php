<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: "chat_messages")]

class Message
    {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column]
    private ?int $message_time = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'chat_messages')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    public function getUser(): ?User
        {
        return $this->user;
        }

    public function setUser(User $user): static
        {
        $this->user = $user;
        $this->user_id = $user->getId();
        return $this;
        }
    public function getId(): ?int
        {
        return $this->id;
        }

    public function getUserId(): ?int
        {
        return $this->user_id;
        }

    public function setUserId(int $user_id): static
        {
        $this->user_id = $user_id;

        return $this;
        }

    public function getMessage(): ?string
        {
        return $this->message;
        }

    public function setMessage(string $message): static
        {
        $this->message = $message;

        return $this;
        }

    public function getMessageTime(): ?int
        {
        return $this->message_time;
        }

    public function setMessageTime(int $message_time): static
        {
        $this->message_time = $message_time;

        return $this;
        }
    }
