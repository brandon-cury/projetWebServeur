<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\Level;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class AdminCommentController extends AbstractController
{
    #[Route('/admin/comment', name: 'app_admin_comment')]
    public function Comment(CommentRepository $repository): Response
    {
        $comments = $repository->findCommentsNotDelete();
        return $this->render('admin/comment.html.twig', [
            'comments' => $comments
        ]);
    }
    #[Route('/admin/comment/message/{id}', name: 'app_admin_comment_send_message')]
    public function sendMessageComment(Request $request, EntityManagerInterface $manager, Comment $comment): Response
    {
        dd('new message email');
        return $this->redirectToRoute('app_admin_comment');
    }
    #[Route('/admin/eyecomment/{id}', name: 'app_admin_eyecomment')]
    public function eyeComment(Request $request, EntityManagerInterface $manager, Comment $comment, MailerInterface $mailer): Response
    {
        $comment->setPublished(!$comment->isPublished());
        $manager->flush();
        $email = (new Email())
            ->from('johndoe@gmail.com')
            ->to('info@webarticle.be')
            ->subject('Question')
            ->text('Je demande des infos...');
        $mailer->send($email);
        return $this->redirectToRoute('app_admin_comment');
    }
    #[Route('/admin/delcomment/{id}', name: 'app_admin_delcomment')]
    public function delLevel(EntityManagerInterface $manager, Level $level): Response
    {
        /*
        $manager->remove($category);
        $manager->flush();
        */

        $level->setName(null)
                ->setPrerequisite(null);
        $manager->flush();
        $this->addFlash('danger', 'votre niveau a été supprimé avec succès');
        return $this->redirectToRoute('app_admin_level');
    }

}
