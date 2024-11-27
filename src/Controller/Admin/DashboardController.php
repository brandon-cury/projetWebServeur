<?php

namespace App\Controller\Admin;

use App\Repository\CourseRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(CourseRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $courses = $repository->findCoursesNotDelete();
        $pagination = $paginator->paginate($courses, $request->query->getInt('page', 1), 15);
        return $this->render('admin/course/course.html.twig', [
            'courses' => $pagination,
        ]);
    }
}
