<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    #[Route('/posts', name: 'app_posts')]
    public function posts(PostRepository $repository, CategoryRepository $categoryRepository): Response
    {
        $posts = $repository->findBy(
            ['isPublished' => true],
            ['createdAt' => 'DESC'],
        );
        return $this->render('post/posts.html.twig', [
            'posts' => $posts,
        ]);
    }


    #[Route('/post/{id}', name: 'app_post')]
    public function post(Post $post): Response
    {
        return $this->render('post/post.html.twig', [
            'post' => $post,
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
