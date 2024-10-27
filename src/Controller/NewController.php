<?php

namespace App\Controller;

use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NewController extends AbstractController
{
    #[Route('/news', name: 'app_news')]
    public function news(NewsRepository $repository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 9;
        $news = $repository->paginateNew($page, $limit);
        return $this->render('new/news.html.twig', [
            'news' => $news
        ]);
    }
}
