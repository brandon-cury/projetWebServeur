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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminCourseRegistrationController extends AbstractController
{
    #[Route('/admin/courses/registrations', name: 'app_admin_all_courses_regis')]
    public function allCoursesRegistrations(RegistrationRepository $repository): Response
    {
        $coursesUsers= $repository->countUsersByCourse();

        return $this->render('admin/course_registration/courses_registrations.html.twig', [
            'coursesUsers' => $coursesUsers
        ]);
    }
    #[Route('/admin/course/registrations/{id}', name: 'app_admin_course_regis')]
    public function courseRegistrations(Course $course, RegistrationRepository $repository): Response
    {
        dd($course->getRegistrations()[0]);

        $coursesUsers= $repository->countUsersByCourse();

        return $this->render('admin/course_registration/courses_registrations.html.twig', [
            'coursesUsers' => $coursesUsers
        ]);
    }

}
