<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    #[Route(path: '/profile', name: 'app_my_account')]
    public function myAccount(Request $request): Response
    {
        $user = $this->getUser();
        if($user){
            $form = $this->createForm(UserType::class, $user, ['attr' => ['readonly' => true]]);
            $form->handleRequest($request);
        }else{
            return $this->redirectToRoute('app_login');
        }
        return$this->render('security/my_account.html.twig',
            ['form'=> $form->createView()]
        );
    }

    #[Route(path: '/profile/update', name: 'app_update_my_account')]
    public function updateMyAccount(EntityManagerInterface $manager, Request $request): Response
    {
        $user = $this->getUser();
        if($user){
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $user->setUpdatedAt(new \DateTimeImmutable());
                $manager->flush();
                $this->addFlash('success', 'Votre profil a été mis à jour');
            }
        }else{
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/update_my_account.html.twig',
            ['form'=> $form->createView()]
        );
    }

    #[Route(path: '/profile/update/password', name: 'app_update_my_password')]
    public function updateMyPassword(EntityManagerInterface $manager, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        if($user){
            $newUser = new User();
            $form = $this->createForm(ChangePasswordType::class, $newUser);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $user->setUpdatedAt(new \DateTimeImmutable());
                $password = $form->get("password")->getData();
                $newPassword = $form->get("new_password")->getData();
                $checkPassword = $form->get("check_password")->getData();
                if ($passwordHasher->isPasswordValid($user, $password) && $newPassword === $checkPassword) {
                    $hashedPassword = $passwordHasher->hashPassword($user, $password);
                    $user->setPassword($hashedPassword);
                    $manager->flush();
                    $this->addFlash('success', 'Votre mot de passe a été mis à jour');
                }else{
                    $this->addFlash('danger', 'vos mot de passe ne sont pas correct');
                }



            }
        }else{
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/update_my_password.html.twig',
            ['form'=> $form->createView()]
        );
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
