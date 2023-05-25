<?php

namespace App\Controller;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\CategorieRepository;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/categorie', name: 'app_categorie')]
    public function index(): Response
    {
        return $this->render('categorie/index.html.twig', [
            'controller_name' => 'CategorieController',
        ]);
    }


    #[Route('/categorie/ajouter', name: 'categories_add')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $libelle = $categorie->getLibelle();
            $categorie->setLibelle(ucfirst($libelle)); // Convertir la première lettre en majuscule
    
            $existingCategorie = $this->entityManager->getRepository(Categorie::class)->findOneBy([
                'libelle' => $categorie->getLibelle(),
            ]);
    
            if ($existingCategorie) {
                $this->addFlash('error', 'La catégorie existe déjà.');
            } else {
                $this->entityManager->persist($categorie);
                $this->entityManager->flush();
    
                $this->addFlash('success', 'La catégorie a été ajouté avec succès.');
            }

            // return $this->redirectToRoute('films_index');
        }

        return $this->render('vod/categories/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
