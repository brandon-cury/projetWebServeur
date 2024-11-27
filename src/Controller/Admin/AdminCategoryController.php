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

class AdminCategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'app_admin_category')]
    public function categories(CategoryRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $categories = $repository->findCetegoriesNotDelete();
        $pagination = $paginator->paginate($categories, $request->query->getInt('page', 1), 15);
        return $this->render('admin/category/category.html.twig', [
            'categories' => $pagination
        ]);
    }
    #[Route('/admin/newcategory', name: 'app_admin_newcategory')]
    public function newCategory(Request $request, EntityManagerInterface $manager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();
            $category->setSlug($slugify->slugify($category->getName()));
            $manager->persist($category);
            $manager->flush();
            $this->addFlash('success', 'votre categorie a bien été ajouté !');
            return $this->redirectToRoute('app_admin_category');
        }
        return $this->render('admin/category/newcategory.html.twig', [
            'form_category'=> $form
        ]);
    }
    #[Route('/admin/editcategory/{id}', name: 'app_admin_editcategory')]
    public function updateCategory(Request $request, EntityManagerInterface $manager, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();
            $category->setSlug($slugify->slugify($category->getName()));
            $manager->persist($category);
            $manager->flush();
            $this->addFlash('success', 'votre categorie a bien été modifié !');
            return $this->redirectToRoute('app_admin_category');
        }
        return $this->render('admin/category/editcategory.html.twig', [
            'form_category'=> $form
        ]);
    }

    #[Route('/admin/delcategory/{id}', name: 'app_admin_delcategory')]
    public function delCategory(EntityManagerInterface $manager, Category $category): Response
    {
        /*
        $manager->remove($category);
        $manager->flush();
        */

        // Supprimer l'image physiquement
        if($category->getImage()){
            $imagePath = $this->getParameter('kernel.project_dir').'/public/images/category/' . $category->getImage();
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $category->setName(null)
                ->setSlug(null)
                ->setDescription(null)
                ->setImage(null);
        $manager->flush();
        $this->addFlash('danger', 'votre categorie a été supprimé avec succès');
        return $this->redirectToRoute('app_admin_category');
    }

}
