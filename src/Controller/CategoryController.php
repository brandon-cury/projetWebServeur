<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    public function categories(CategoryRepository $repository): Response
    {
        $categories = $repository->findCetegoriesNotDelete();

        return $this->render('partials/categories.html.twig', [
            "categories" => $categories,
        ]);
    }
}
