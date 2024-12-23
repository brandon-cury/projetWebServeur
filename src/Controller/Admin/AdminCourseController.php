<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Course;
use App\Form\CategoryType;
use App\Form\CourseType;
use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminCourseController extends AbstractController
{
    public  function __construct(private readonly sluggerInterface $slugger)
    {

    }
    #[Route('/admin/course', name: 'app_admin_course')]
    public function courses(CourseRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $courses = $repository->findCoursesNotDelete();
        $pagination = $paginator->paginate($courses, $request->query->getInt('page', 1), 15);
        return $this->render('admin/course/course.html.twig', [
            'courses' => $pagination
        ]);
    }
    #[Route('/admin/newcourse', name: 'app_admin_newcourse')]
    public function newCourse(Request $request, EntityManagerInterface $manager): Response
    {
        $course = new Course();

        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $course->setPublished(true)
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setSlug($this->slugger->slug($course->getName()))
                    ;
            $manager->persist($course);
            $manager->flush();
            $this->addFlash('success', 'votre cours a été ajouté avec succès!');
            return $this->redirectToRoute('app_admin_course');
        }
        return $this->render('admin/course/newcourse.html.twig', [
            'form'=> $form
        ]);
    }
    #[Route('/admin/editcourse/{id}', name: 'app_admin_editcourse')]
    public function editCourse(Request $request, EntityManagerInterface $manager, Course $course): Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();
            $course->setSlug($slugify->slugify($course->getName()))
                ->setUpdatedAt(new \DateTimeImmutable());
            $manager->flush();
            $this->addFlash('success', 'votre cours a été modifié avec succès!');
            return $this->redirectToRoute('app_admin_course');
        }
        return $this->render('admin/course/editcourse.html.twig', [
            'form'=> $form,

        ]);
    }
    #[Route('/admin/eyecourse/{id}', name: 'app_admin_eyecourse')]
    public function eyeCourse(Request $request, EntityManagerInterface $manager, Course $course): Response
    {
        $course->setPublished(!$course->isPublished());
        $manager->flush();
        return $this->redirectToRoute('app_admin_course');
    }

    #[Route('/admin/delcourse/{id}', name: 'app_admin_delcourse')]
    public function delCourse(EntityManagerInterface $manager, Course $course): Response
    {
        // Supprimer l'image physiquement
        if($course->getImage()){
            $imagePath = $this->getParameter('kernel.project_dir').'/public/images/cours/' . $course->getImage();
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        // Supprimer le programe physiquement
        if($course->getProgram()){
            $programPath = $this->getParameter('kernel.project_dir').'/public/programs/' . $course->getProgram();
            if (file_exists($programPath)) {
                unlink($programPath);
            }
        }
        //supprimer les utilisateurs du cours
        $courses_users = $course->getUsers();
        foreach ($courses_users as $user){
            $user->removeCourse($course);
        }
        //supprimer les paniers lié au cours
        $baskets = $course->getBaskets();
        foreach ($baskets as $basket){
            $manager->remove($basket);
        }
        $manager->flush();
        //supprimer en DB sans cassure vu que la table course est lié au commentaire, level...
        $course->setName(null)
            ->setCategory(null)
            ->setLevel(null)
            ->setSmallDescription(null)
            ->setFullDescription(null)
            ->setDuration(null)
            ->setPrice(null)
            ->setPublished(null)
            ->setSlug(null)
            ->setImage(null)
            ->setUpdatedAt(new \DateTimeImmutable())
            ->setProgram(null)
            ->setImageFile(null)
        ;
        $this->addFlash('danger', 'votre cours a été supprimé avec succès');
        $manager->flush();
        return $this->redirectToRoute('app_admin_course');
    }

}
