<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Course;
use App\Form\CategoryType;
use App\Form\CourseType;
use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;
use App\Repository\RegistrationRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminUserCourseController extends AbstractController
{
    #[Route('/admin/users/courses', name: 'app_admin_all_courses_regis')]
    public function allCoursesRegistrations(CourseRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $coursesUsers= $repository->getCoursesWithUserCount();
        $pagination = $paginator->paginate($coursesUsers, $request->query->getInt('page', 1), 10);
        return $this->render('admin/user_course/users_courses.html.twig', [
            'coursesUsers' => $pagination
        ]);
    }
    #[Route('/admin/users/course/{id}', name: 'app_admin_course_regis')]
    public function courseRegistrations(Course $course): Response
    {
        return $this->render('admin/user_course/users_course.html.twig', [
            'course' => $course
        ]);
    }

}
