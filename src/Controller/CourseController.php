<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Course;
use App\Form\CommentType;
use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    public function course(string $slug, CourseRepository $repository, EntityManagerInterface $manager, Request $request, Security $security): Response
    {
        $course = $repository->findOneBy(
            ['slug'=> $slug]
        );
        if(!$course){
            throw new NotFoundHttpException('Pas de cours trouvé');
        }
        //formulaire de commentaire
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $user = $security->getUser();
        if($form->isSubmitted() && $form->isValid()){
            if(!$user){
                return $this->redirectToRoute('app_admin_category');
            }
            //verifier si c'est une insersion simple
            $updateId = $form->get("updateId")->getData();
            if(!$updateId){
                //on recupere le parent id
                $parantId = $form->get("parentId")->getData();
                $parent = null;
                if($parantId){
                    $parent = $manager->getRepository(Comment::class)->find($parantId);
                }
                $comment->setCreatedAt(new \DateTimeImmutable())
                    ->setCourse($course)
                    ->setParent($parent)
                    ->setPublished(false);
                    $comment->setUser($user);
                    $manager->persist($comment);
                    $manager->flush();
                    $this->addFlash('success', 'Votre commentaire à bien été enregistré');

            }
            else{
                $comment2 = $manager->getRepository(Comment::class)->find($updateId);
                if($user->getId() == $comment2->getUser()->getId()){

                    $comment2->setContent($form->get('content')->getData());
                    $manager->persist($comment2);
                    $manager->flush();
                    $this->addFlash('success', 'Votre commentaire à été modifié avec sucès');
                }
            }
            return $this->redirectToRoute('app_course', ['slug'=> $course->getSlug()]);

        }

        return $this->render('course/detail.html.twig', [
            'course' => $course,
            'form' => $form->createView()
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
