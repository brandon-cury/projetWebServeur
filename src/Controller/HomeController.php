<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(CourseRepository $repository, NewsRepository $newsRepository): Response
    {
        $courses = $repository->findBy(
            ['is_published'=> true],
            ['created_at' => 'DESC'],
            3
        );
        $news = $newsRepository->findBy(
            ['is_published'=> true],
            ['created_at' => 'DESC'],
            4
        );

        return $this->render('home/index.html.twig', [
            'courses' => $courses,
            'news' => $news
        ]);
    }
}
