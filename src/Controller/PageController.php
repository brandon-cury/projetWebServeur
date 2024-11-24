<?php

namespace App\Controller;

use App\Class\Contact;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('page/about.html.twig', [
        ]);
    }
    #[Route('/contact', name: 'app_contact')]
    public function contact(MailerInterface $mailer, Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new Email())
                ->from($contact->getEmail())
                ->to($contact->getAdministrator()->getEmail())
                ->subject($contact->getSubject())
                ->html($contact->getMessage());
            ;
            $mailer->send($email);
            $this->addFlash('success', 'Votre message a bien été envoyé !');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);

    }
}
