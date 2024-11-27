<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdminCommentController extends AbstractController
{
    #[Route('/admin/comment', name: 'app_admin_comment')]
    public function comments(CommentRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $comments = $repository->findCommentsNotDelete();
        $pagination = $paginator->paginate($comments, $request->query->getInt('page', 1), 15);
        return $this->render('admin/comment/comments.html.twig', [
            'comments' => $pagination
        ]);
    }
    #[Route('/admin/comment/{id}', name: 'app_admin_one_comment')]
    public function oneComment(Comment $comment, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('admin/comment/comment.html.twig', [
            'comment' => $comment
        ]);
    }
    #[Route('/admin/comment/message/{id}', name: 'app_admin_comment_send_message')]
    public function sendMessageComment(Request $request, EntityManagerInterface $manager, Comment $comment, MailerInterface $mailer, Security $security): Response
    {
        // Créez l'URL vers la page de votre site
        $url = $this->generateUrl('app_course', ['slug' => $comment->getCourse()->getSlug(), 'id'=> $comment->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $user = $security->getUser();
        $email = (new Email())
            ->from($user->getEmail())
            ->to($comment->getUser()->getEmail())
            ->subject('Commentaire non publié - WebStudent.com')
            ->html('<h1>Bonjour '. $comment->getUser()->getFirstName() .', votre commentaire a été retiré.</h1> <p>Veuillez le modifier et nous le soumettre à nouveau !</p> <a style="text-decoration: none" href="'. $url .'">aller au commentaire</a><h2>Merci pour votre commentaire:</h2> <div>'. $comment->getContent() .'</div>');
        $mailer->send($email);

        //modification du send
        $comment->setSend(!$comment->isSend());
        $manager->flush();

        //flash
        $this->addFlash('success', 'Message envoyé avec succès !');

        return $this->redirectToRoute('app_admin_comment');
    }
    #[Route('/admin/eyecomment/{id}', name: 'app_admin_eyecomment')]
    public function eyeComment(Request $request, EntityManagerInterface $manager, Comment $comment, MailerInterface $mailer, Security $security): Response
    {
        $comment->setPublished(!$comment->isPublished());
        $manager->flush();
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();
        $email = (new Email())
            ->from($user->getEmail())
            ->to($comment->getUser()->getEmail());

        // Créez l'URL vers la page de votre site
         $url = $this->generateUrl('app_course', ['slug' => $comment->getCourse()->getSlug(), 'id'=> $comment->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            if($comment->isPublished()){
                $email->subject('Commentaire publié - WebStudent.com')
                    ->html('<h1>Bonjour '. $comment->getUser()->getFirstName() .', votre commentaire a été publié avec succès !</h1> <a style="text-decoration: none" href="'. $url .'">aller au commentaire</a><h2>Merci pour votre commentaire:</h2> <div>'. $comment->getContent() .'</div>');
            }else{
                $email->subject('Commentaire non publié - WebStudent.com')
                ->html('<h1>Bonjour '. $comment->getUser()->getFirstName() .', votre commentaire a été retiré.</h1> <p>Veuillez le modifier et nous le soumettre à nouveau !</p> <a style="text-decoration: none" href="'. $url .'">aller au commentaire</a><h2>Merci pour votre commentaire:</h2> <div>'. $comment->getContent() .'</div>');
            }
        $mailer->send($email);
        return $this->redirectToRoute('app_admin_comment');
    }
    #[Route('/admin/delcomment/{id}', name: 'app_admin_delcomment')]
    public function delComment(EntityManagerInterface $manager, Comment $comment): Response
    {
        /*
        $manager->remove($category);
        $manager->flush();
        */

        $comment->setContent(null)
                ->setPublished(false)
                ->setSend(false);
        $manager->flush();
        $this->addFlash('danger', 'Le commentaire a été supprimé avec succès');
        return $this->redirectToRoute('app_admin_comment');
    }

}
