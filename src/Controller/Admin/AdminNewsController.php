<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Course;
use App\Entity\News;
use App\Form\CategoryType;
use App\Form\CourseType;
use App\Form\NewsType;
use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;
use App\Repository\NewsRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminNewsController extends AbstractController
{
    #[Route('/admin/new', name: 'app_admin_news')]
    public function news(NewsRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $news = $repository->findBy(
            [],
            ['created_at' => 'desc'],
        );
        $pagination = $paginator->paginate($news, $request->query->getInt('page', 1), 10);
        return $this->render('admin/news.html.twig', [
            'news' => $pagination
        ]);
    }
    #[Route('/admin/newnews', name: 'app_admin_newnews')]
    public function newNews(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $news->setSlug($slugger->slug($news->getName()))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setPublished(false)
            ;
            $manager->persist($news);
            $manager->flush();
            $this->addFlash('success', 'la nouvelle actualité a bien été ajouté !');
            return $this->redirectToRoute('app_admin_news');
        }
        return $this->render('admin/newnews.html.twig', [
            'form'=> $form
        ]);
    }
    #[Route('/admin/eyenews/{id}', name: 'app_admin_eyenews')]
    public function eyenews(Request $request, EntityManagerInterface $manager, News $news): Response
    {
        $news->setPublished(!$news->isPublished());
        $manager->flush();
        return $this->redirectToRoute('app_admin_news');
    }
    #[Route('/admin/editnews/{id}', name: 'app_admin_editnews')]
    public function updateNews(Request $request, EntityManagerInterface $manager, News $news, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $news->setSlug($slugger->slug($news->getName()));
            $manager->persist($news);
            $manager->flush();
            $this->addFlash('success', 'l\'actualité a bien été modifié !');
            return $this->redirectToRoute('app_admin_news');
        }
        return $this->render('admin/editnews.html.twig', [
            'form'=> $form
        ]);
    }

    #[Route('/admin/delnews/{id}', name: 'app_admin_delnews')]
    public function delNews(EntityManagerInterface $manager, News $news): Response
    {

        $manager->remove($news);
        $manager->flush();
        $this->addFlash('danger', 'l\'actualité a bien été supprimé !');
        return $this->redirectToRoute('app_admin_news');
    }

}
