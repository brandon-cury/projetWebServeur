<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Course;
use App\Repository\BasketRepository;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BasketController extends AbstractController
{
    public function nomberCourses(BasketRepository $repository, Security $security): Response
    {
        $user = $security->getUser();
        $count = 0;
        if($user){
            $baskets = $repository->findBy(
                [
                    'user'=> $user->getId(),
                ],
            );
            $count = count($baskets);
            if($count > 99){
                $count = '99+';
            }
        }


        return $this->render('partials/numbercourses.html.twig', [
            "count_basket" => $count,
        ]);
    }
    #[Route('/basket/{id}', name: 'app_basket_add')]
    public function addCourseBasket(Course $course, CourseRepository $repository, Security $security, EntityManagerInterface $manager): Response
    {

        $basket = new Basket();
        $user = $security->getUser();
        if($user){
            $findBasket = $manager->getRepository(Basket::class)->findOneBy(
                [
                    'user' => $user->getId(),
                    'course' => $course->getId()
                ]
            );
            $userEnrolledInCourse = $repository->isUserEnrolledInCourse($user, $course);
            if($findBasket == null && !$userEnrolledInCourse){
                $basket->setCourse($course)
                    ->setUser($user)
                    ->setCreatedAt(new \DateTimeImmutable())
                ;
                $manager->persist($basket);
                $manager->flush();
                $this->addFlash('success', 'La formation a bien été ajouter au panier');
            }else{
                $this->addFlash('info', 'Cette formation a déjà été ajouté dans votre panier !');
            }


        }else{
            $this->addFlash('info', 'Veillez-vous identifier pour vous enregistrer à une formation');
            return $this->redirectToRoute('app_login');
        }

        return $this->redirectToRoute('app_course', ['slug'=> $course->getSlug()]);
    }

    #[Route('/basket', name: 'app_basket')]
    public function basketview(BasketRepository $repository, Security $security, PaginatorInterface $paginator, Request $request)
    {
        $user = $security->getUser();
        if($user == null) {
            $this->addFlash('info', 'Veillez-vous identifier pour voir votre panier');
            return $this->redirectToRoute('app_login');
        }

        $baskets = $repository->findBy(['user'=> $user]);
        $pagination = $paginator->paginate($baskets, $request->query->getInt('page', 1), 10);
        $total = 0;
        foreach ($baskets as $basket){
            $total += $basket->getCourse()->getPrice();
        }
        return $this->render('basket/basket.html.twig', [
            'baskets' => $pagination,
            'total' => $total
        ]);
    }
    #[Route('/basket/delete/{id}', name: 'app_basket_del')]
    public function delBasket(EntityManagerInterface $manager, Basket $basket, Security $security): Response
    {
        $user = $security->getUser();
        if($user == null) {
            $this->addFlash('info', 'Veillez-vous identifier pour voir votre panier');
            return $this->redirectToRoute('app_login');
        }
        if($basket->getUser() != $user){
            $this->addFlash('danger', 'Suppression impossible!');
            return $this->redirectToRoute('app_basket');
        }
        $manager->remove($basket);
        $manager->flush();
        $this->addFlash('success', 'la formation a bien été supprimé de votre panier !');
        return $this->redirectToRoute('app_basket');
    }

}
