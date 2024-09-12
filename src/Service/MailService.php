<?php 
namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
class MailService extends AbstractController
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmailsToUsers(array $emails, string $postTitle, string $commentText)
    {
        foreach ($emails as $email) {
            $message = (new Email())
                ->from('admin-noREPLY@example.com')
                ->to($email['email'])
                ->subject('New Comment on Post: ' . $postTitle)
                ->html("<p>A new comment has been on the post: <strong>{$postTitle}</strong></p>
                        <p>Comment: {$commentText}</p>");

                        try {
                            $this->mailer->send($message);
                        } catch (TransportExceptionInterface $e) {
                            // some error prevented the email sending; display an
                            // error message or try to resend the message
                        }
        }
    }
}
