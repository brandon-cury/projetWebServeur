<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Course;
use App\Entity\Registration;
use App\Repository\BasketRepository;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationCourseController extends AbstractController
{
    #[Route('registration/course/{id}', name: 'app_course_registration_add')]
    public function addCourseRegistration(Basket $basket, Security $security, EntityManagerInterface $manager): Response
    {
        $registration = new Registration();
        $user = $security->getUser();
        if($user){
            $findRegistration = $manager->getRepository(Registration::class)->findOneBy(
                [
                    'user' => $user->getId(),
                    'course' => $basket->getCourse()->getId()
                ]
            );
            if($findRegistration == null){
                $registration->setCourse($basket->getCourse())
                    ->setUser($user)
                    ->setCreatedAt(new \DateTimeImmutable())
                ;
                $manager->persist($registration);
                $manager->flush();
                $this->addFlash('success', 'Vous êtes bien inscrit à la formation');
                return $this->redirectToRoute('app_basket_del', ['id' => $basket->getId()]);

            }else{
                $this->addFlash('info', 'Vous êtes déjà inscrit à cette formation !');
            }


        }else{
            $this->addFlash('info', 'Veillez-vous identifier pour vous inscrire à une formation');
            return $this->redirectToRoute('app_login');
        }

        return $this->redirectToRoute('app_basket');
    }

    #[Route('registration/course', name: 'app_course_registration')]
    public function registrationView(RegistrationRepository $repository, Security $security, PaginatorInterface $paginator, Request $request)
    {
        $user = $security->getUser();
        if($user == null) {
            $this->addFlash('info', 'Veillez-vous identifier pour voir vos cours');
            return $this->redirectToRoute('app_login');
        }

        $registration = $repository->findBy(['user'=> $user]);
        $pagination = $paginator->paginate($registration, $request->query->getInt('page', 1), 10);
        return $this->render('course/courses_registration.html.twig', [
            'registration' => $pagination,
        ]);
    }
    #[Route('/registration/course/delete/{id}', name: 'app_registration_course_del')]
    public function delNews(EntityManagerInterface $manager, Registration $registration, Security $security): Response
    {
        $user = $security->getUser();
        if($user == null) {
            $this->addFlash('info', 'Veillez-vous identifier pour voir votre panier');
            return $this->redirectToRoute('app_login');
        }
        if($registration->getUser() != $user){
            $this->addFlash('danger', 'Suppression impossible!');
            return $this->redirectToRoute('app_course_registration');
        }
        $manager->remove($registration);
        $manager->flush();
        $this->addFlash('success', 'Vous avez bien été supprimé de la formation !');
        return $this->redirectToRoute('app_course_registration');
    }

}
