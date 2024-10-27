<?php

namespace App\Controller;

use App\Entity\Course;
use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CourseController extends AbstractController
{
    #[Route('/courses/{category_slug?}', name: 'app_courses')]
    public function courses(CourseRepository $repository, Request $request, ?string $category_slug, CategoryRepository $categoryRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $category_url = $request->query->getInt('category');
        $categorie = $categoryRepository->findOneBy(
            ['name'=> $category_slug]
        );
        $limit = 10;
        $courses = $repository->paginateCourse($page, $limit, $categorie);
        return $this->render('course/courses.html.twig', [
            'courses' => $courses,
        ]);
    }



    #[Route('/course/{slug}', name: 'app_course')]
    public function course(string $slug, CourseRepository $repository): Response
    {
        $course = $repository->findBy(
            ['slug'=> $slug]
        )[0];
        return $this->render('course/detail.html.twig', [
            'course' => $course,
        ]);
    }
    #[Route('/posts/{category}', name: 'app_post_category')]
    public function postsCat(PostRepository $repository, int $category): Response
    {
        $posts =  $repository->findBy(
            ['category' => $category, 'isPublished' => true],
            ['title' => 'ASC'],


        );
        dump($posts);
        return $this->render('post/posts.html.twig', [
            'posts' => $posts,
        ]);
    }
}
