<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Course;
use App\Entity\Registration;
use App\Repository\BasketRepository;
use App\Repository\CourseRepository;
use App\Repository\RegistrationRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;

class UserCourseController extends AbstractController
{
    #[Route('user/course/{id}', name: 'app_course_registration_add')]
    public function addCourseRegistration(Basket $basket, Security $security, CourseRepository $repository, EntityManagerInterface $manager, EmailVerifier $emailVerifier): Response
    {
        $user = $security->getUser();
        if($user){
            if($user->isVerified()){
                $userEnrolledInCourse = $repository->isUserEnrolledInCourse($user, $basket->getCourse());
                if(!$userEnrolledInCourse){
                    $user->addCourse($basket->getCourse());
                    $manager->persist($user);
                    $manager->flush();

                    $this->addFlash('success', 'Vous êtes bien inscrit à la formation');
                    return $this->redirectToRoute('app_basket_del', ['id' => $basket->getId()]);

                }else{
                    $this->addFlash('info', 'Vous êtes déjà inscrit à cette formation !');
                }
            }else{
                // generate a signed url and email it to the user
                $emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('service@webStudent.com', 'webStuden'))
                        ->to((string) $user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );
                $this->addFlash('info', 'Un mail de vérification de votre compte vous a été envoyé. Veillez vérifier votre compte pour avoir accès à nos formations.');
            }


        }else{
            $this->addFlash('info', 'Veillez-vous identifier pour vous inscrire à une formation');
            return $this->redirectToRoute('app_login');
        }

        return $this->redirectToRoute('app_basket');
    }

    #[Route('user/course', name: 'app_course_registration')]
    public function registrationView(Security $security, PaginatorInterface $paginator, Request $request)
    {
        $user = $security->getUser();
        if($user == null) {
            $this->addFlash('info', 'Veillez-vous identifier pour voir vos cours');
            return $this->redirectToRoute('app_login');
        }

        $courses = $user->getCourse();
        $pagination = $paginator->paginate($courses, $request->query->getInt('page', 1), 10);
        return $this->render('course/courses_user.html.twig', [
            'courses' => $pagination,
        ]);
    }
    #[Route('user/course/delete/{id}', name: 'app_registration_course_del')]
    public function delNews(Course $course, CourseRepository $repository, EntityManagerInterface $manager, Security $security): Response
    {
        $user = $security->getUser();
        if($user == null) {
            $this->addFlash('info', 'Veillez-vous identifier pour voir votre panier');
            return $this->redirectToRoute('app_login');
        }
        if(!$repository->isUserEnrolledInCourse($user, $course)){
            $this->addFlash('danger', 'Suppression impossible!');
            return $this->redirectToRoute('app_course_registration');
        }
        $user->removeCourse($course);
        $manager->persist($user);
        $manager->flush();
        $this->addFlash('success', 'Vous avez bien été supprimé de la formation !');
        return $this->redirectToRoute('app_course_registration');
    }

}
