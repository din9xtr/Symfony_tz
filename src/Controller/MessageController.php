<?php

namespace App\Controller;
use App\Repository\MessageRepository;
use App\Entity\Message;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Doctrine\ORM\EntityManagerInterface;
class MessageController extends AbstractController
    {
    private HubInterface $publisher;
    private EntityManagerInterface $entityManager;

    public function __construct(HubInterface $publisher, EntityManagerInterface $entityManager)
        {
        $this->publisher = $publisher;
        $this->entityManager = $entityManager;
        }
    #[Route('/chat', name: 'app_chat')]
    public function index(MessageRepository $messageRepository): Response
        {
        $recentMessages = $messageRepository->getRecentMessagesObj();
        if (!$recentMessages) {
            $recentMessages = [];
            }
        $recentMessages = array_reverse($recentMessages);

        return $this->render('message/index.html.twig', [
            'recentMessages' => $recentMessages,
        ]);
        }
    #[Route('/send', name: 'chat_send', methods: 'POST')]
    public function sendMessage(Request $request): Response
        {

        $content = $request->request->get('content');
        $user = $this->getUser();

        $message = new Message();
        $message->setUser($user)
            ->setMessage($content)
            ->setMessageTime(time());

        $this->entityManager->persist($message);
        $this->entityManager->flush();
        //  Mercure
        $update = new Update(
            'chat',
            json_encode(['content' => $content, 'user' => $user->getLogin()])
        );
        $this->publisher->publish($update);

        return new Response('Message sent', Response::HTTP_OK);
        }
    }
