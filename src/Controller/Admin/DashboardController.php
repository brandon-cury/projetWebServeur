<?php

namespace App\Controller\Admin;

use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(CourseRepository $repository): Response
    {
        $courses = $repository->findBy(
            [],
            ['created_at' => 'DESC'],

        );
        return $this->render('admin/course.html.twig', [
            'courses' => $courses,
        ]);
    }
}
