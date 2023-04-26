<?php

namespace App\Controller;

use App\Entity\Salle;
use App\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ProfilController extends AbstractController
{

    private $entityManager;

    #[Route('/profil', name: 'app_profil')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $role = new Role();
        $role->setLibelle("admin");

        $entityManager->persist($role);
        $entityManager->flush();

        $entityManager->refresh($role);


        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }
}
