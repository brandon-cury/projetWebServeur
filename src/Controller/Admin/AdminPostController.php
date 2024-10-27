<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\CategoryType;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminPostController extends AbstractController
{
    #[Route('/admin/post', name: 'app_admin_post')]
    public function posts(PostRepository $repository): Response
    {
        $posts = $repository->findBy(
            [],
            ['createdAt' => 'DESC'],

        );
        return $this->render('admin/post.html.twig', [
            'posts' => $posts
        ]);
    }
    #[Route('/admin/newpost', name: 'app_admin_newpost')]
    public function newPost(Request $request, EntityManagerInterface $manager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setPublished(true);
            $manager->persist($post);
            $manager->flush();
            return $this->redirectToRoute('app_admin_post');
        }
        return $this->render('admin/newpost.html.twig', [
            'form'=> $form
        ]);
    }
    #[Route('/admin/editpost/{id}', name: 'app_admin_editpost')]
    public function editPost(Request $request, EntityManagerInterface $manager, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setPublished(true);
            $manager->flush();
            return $this->redirectToRoute('app_admin_post');
        }
        return $this->render('admin/editpost.html.twig', [
            'form'=> $form,

        ]);
    }
    #[Route('/admin/eyepost/{id}', name: 'app_admin_eyepost')]
    public function eyePost(Request $request, EntityManagerInterface $manager, Post $post): Response
    {
        $post->setPublished(!$post->isPublished());
        $manager->flush();
        return $this->redirectToRoute('app_admin_post');
    }

    #[Route('/admin/delpost/{id}', name: 'app_admin_delpost')]
    public function delPost(EntityManagerInterface $manager, Post $post): Response
    {
        $manager->remove($post);
        $manager->flush();
        return $this->redirectToRoute('app_admin_post');
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
