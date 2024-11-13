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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminCourseController extends AbstractController
{
    #[Route('/admin/course', name: 'app_admin_course')]
    public function courses(CourseRepository $repository): Response
    {
        $courses = $repository->findCoursesNotDelete();
        return $this->render('admin/course.html.twig', [
            'courses' => $courses
        ]);
    }
    #[Route('/admin/newcourse', name: 'app_admin_newcourse')]
    public function newCourse(Request $request, EntityManagerInterface $manager): Response
    {
        $course = new Course();

        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();
            $course->setPublished(true)
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setSlug($slugify->slugify($course->getName()))
                    ;
            $manager->persist($course);
            $manager->flush();
            $this->addFlash('success', 'votre cours a été ajouté avec succès!');
            return $this->redirectToRoute('app_admin_course');
        }
        return $this->render('admin/newcourse.html.twig', [
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
        return $this->render('admin/editcourse.html.twig', [
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
        $this->addFlash('danger', 'votre cours a été supprimer avec succès');
        $manager->flush();
        return $this->redirectToRoute('app_admin_course');
    }


    #[Route('/admin/category', name: 'app_admin_category')]
    public function categories(CategoryRepository $repository): Response
    {
        $categories = $repository->findAll();
        return $this->render('admin/category.html.twig', [
            'categories' => $categories
        ]);
    }
    #[Route('/admin/newcategory', name: 'app_admin_newcategory')]
    public function newCategory(Request $request, EntityManagerInterface $manager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('app_admin_category');
        }
        return $this->render('admin/newcategory.html.twig', [
            'form_category'=> $form
        ]);
    }
    #[Route('/admin/editcategory/{id}', name: 'app_admin_editcategory')]
    public function updateCategory(Request $request, EntityManagerInterface $manager, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('app_admin_category');
        }
        return $this->render('admin/editcategory.html.twig', [
            'form_category'=> $form
        ]);
    }

    #[Route('/admin/delcategory/{id}', name: 'app_admin_delcategory')]
    public function delCategory(EntityManagerInterface $manager, Category $category): Response
    {
        $manager->remove($category);
        $manager->flush();
        return $this->redirectToRoute('app_admin_category');
    }

}
