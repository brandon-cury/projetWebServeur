<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\CourseRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
        $limit = 9;
        $courses = $repository->paginateCourse($page, $limit, $categorie);
        if(!$categorie) $categorie = (new Category())->setName('Tous');
        $categories = $categoryRepository->findAll();
        $categories[] = (new Category())->setName('Tous');
        return $this->render('course/courses.html.twig', [
            'courses' => $courses,
            'categories' => $categories,
            'categorie' => $categorie
        ]);
    }



    #[Route('/course/{slug}', name: 'app_course')]
    public function course(string $slug, CourseRepository $repository, EntityManagerInterface $manager, MailerInterface $mailer, CommentRepository $commentRepository, UserRepository $userRepository, Request $request, Security $security): Response
    {
        $user = $security->getUser();

        $course = $repository->findOneBy(
            ['slug'=> $slug]
        );
        $comments = $course->getComments();
        //comtage du nombre d'étoile
        $rating = 0;
        $comments_count = count($comments);
        foreach ($comments as $comment){
            $rating += $comment->getRating();
        };
        $ratings_active = ceil($rating/$comments_count);
        $ratings= [
            'actifs' => $ratings_active,
            'numbers' => $comments_count
        ];


        if(!$course){
            throw new NotFoundHttpException('Pas de cours trouvé');
        }
        //formulaire de commentaire
        $comment = new Comment();
        if($request->query->get('id')){
            $find_comment = $manager->getRepository(Comment::class)->find($request->query->get('id'));
            if($find_comment){
                if($user->getId() == $find_comment->getUser()->getId()){
                    $comment = $find_comment;
                }
            }
        }


        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

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
                    ->setPublished(false)
                    ->setSend(false)
                    ->setUser($user);
                    $manager->persist($comment);
                    $manager->flush();
                    $this->addFlash('success', 'Votre commentaire à bien été enregistré');

            }
            else{
                $comment2 = $manager->getRepository(Comment::class)->find($updateId);
                if($user->getId() == $comment2->getUser()->getId()){

                    $comment2->setContent($form->get('content')->getData())
                            ->setPublished(false)
                            ->setSend(false)
                            ->setRating($form->get('rating')->getData());
                    $manager->persist($comment2);
                    $manager->flush();
                    $this->addFlash('success', 'Votre commentaire à été modifié avec sucès');
                }
            }

            $teams = $userRepository->findTeams();
            foreach ($teams as $team){
                $email = (new Email())
                    ->from($user->getEmail())
                    ->to($team->getEmail());
                    if(!$updateId){
                        $url = $this->generateUrl('app_admin_one_comment', ['id' => $comment->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
                        $email->subject('Nouveau commentaire - WebStudent.com')
                            ->html('<h1>Bonjour '. $team->getFirstName() .', le nouveau commentaire de'. $user->getFirstName()  .'attend votre approbation pour être publié.</h1> <a style="text-decoration: none" href="'. $url .'">gérer le commentaire</a><h2>Voici le commentaire :</h2> <div>'. $comment->getContent() .'</div>');
                    }else{
                        $url = $this->generateUrl('app_admin_one_comment', ['id' => $comment2->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
                        $email->subject('Commentaire Modifié - WebStudent.com')
                            ->html('<h1>Bonjour '. $team->getFirstName() .'</h1><p>'. $user->getFirstName() . ' a bien modifié son commentaire. Son commmentaire attend votre approbation pour être publié.</p> <a style="text-decoration: none" href="'. $url .'">gérer le commentaire</a><h2>Voici le commentaire :</h2> <div>'. $comment2->getContent() .'</div>');
                    }

                $mailer->send($email);
            }

            return $this->redirectToRoute('app_course', ['slug'=> $course->getSlug()]);

        }

        return $this->render('course/detail.html.twig', [
            'course' => $course,
            'form' => $form->createView(),
            'ratings' => $ratings
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
