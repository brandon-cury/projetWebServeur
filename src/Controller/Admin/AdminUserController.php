<?php

namespace App\Controller\Admin;


use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\RoleType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminUserController extends AbstractController
{
    public  function __construct(private readonly sluggerInterface $slugger)
    {

    }
    #[Route('/admin/user', name: 'app_admin_user')]
    public function users(UserRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $users = $repository->findAll();
        $pagination = $paginator->paginate($users, $request->query->getInt('page', 1), 10);
        return $this->render('admin/user/user.html.twig', [
            'users' => $pagination
        ]);
    }

    #[Route('/admin/user/teams', name: 'app_admin_user_teams')]
    public function teams(UserRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $users = $repository->findTeams();
        $pagination = $paginator->paginate($users, $request->query->getInt('page', 1), 10);
        return $this->render('admin/user/team.html.twig', [
            'users' => $pagination
        ]);
    }
    #[Route('/admin/newuser', name: 'app_admin_newuser')]
    public function newUser(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $passwordCheck = $form->get("check_password")->getData();
            if ($plainPassword === $passwordCheck) {
                // encode the plain password
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword))
                    ->setDisabled(false)
                    ->setRoles(['ROLE_USER'])
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setUpdatedAt(new \DateTimeImmutable())
                    ->setLastLogAt(new \DateTimeImmutable());

                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', 'L’utilisateur a bien été enregistré');
                return $this->redirectToRoute('app_admin_user');
            }
        }
        return $this->render('admin/user/newuser.html.twig', [
            'form'=> $form
        ]);
    }
    #[Route('/admin/super/editroleuser/{id}', name: 'app_admin_editroleuser')]
    public function editRoleUser(Request $request, EntityManagerInterface $manager, User $user): Response
    {
        $form = $this->createForm(RoleType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $roles = array_values($form->get("roles")->getData());
            $user->setRoles($roles);
            $manager->flush();
            $this->addFlash('success', 'Le role a été modifié avec succès!');
            return $this->redirectToRoute('app_admin_user');
        }
        return $this->render('admin/user/editroleuser.html.twig', [
            'form_roles'=> $form,
        ]);
    }
    #[Route('/admin/eyeuser/{id}', name: 'app_admin_eyeuser')]
    public function eyeCourse(Request $request, EntityManagerInterface $manager, User $user): Response
    {
        $user->setDisabled(!$user->isDisabled());
        $manager->flush();
        return $this->redirectToRoute('app_admin_user');
    }


}
