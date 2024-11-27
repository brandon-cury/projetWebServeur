<?php

namespace App\Controller\Admin;

use App\Entity\Level;
use App\Form\LevelType;
use App\Repository\LevelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminLevelController extends AbstractController
{
    #[Route('/admin/level', name: 'app_admin_level')]
    public function level(LevelRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $levels = $repository->findLevelsNotDelete();
        $pagination = $paginator->paginate($levels, $request->query->getInt('page', 1), 15);
        return $this->render('admin/level/level.html.twig', [
            'levels' => $pagination
        ]);
    }
    #[Route('/admin/newlevel', name: 'app_admin_newlevel')]
    public function newLevel(Request $request, EntityManagerInterface $manager): Response
    {
        $level = new Level();
        $form = $this->createForm(LevelType::class, $level);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($level);
            $manager->flush();
            $this->addFlash('success', 'votre niveau a bien été ajouté !');
            return $this->redirectToRoute('app_admin_level');
        }
        return $this->render('admin/level/newlevel.html.twig', [
            'form_level'=> $form
        ]);
    }
    #[Route('/admin/editlevel/{id}', name: 'app_admin_editlevel')]
    public function updateLevel(Request $request, EntityManagerInterface $manager, Level $level): Response
    {
        $form = $this->createForm(LevelType::class, $level);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($level);
            $manager->flush();
            $this->addFlash('success', 'votre niveau a bien été modifié !');
            return $this->redirectToRoute('app_admin_level');
        }
        return $this->render('admin/level/editlevel.html.twig', [
            'form_level'=> $form
        ]);
    }

    #[Route('/admin/dellevel/{id}', name: 'app_admin_dellevel')]
    public function delLevel(EntityManagerInterface $manager, Level $level): Response
    {
        /*
        $manager->remove($category);
        $manager->flush();
        */

        $level->setName(null)
                ->setPrerequisite(null);
        $manager->flush();
        $this->addFlash('danger', 'votre niveau a été supprimé avec succès');
        return $this->redirectToRoute('app_admin_level');
    }

}
