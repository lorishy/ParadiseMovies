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


    #[Route('/categories', name: 'categories_index')]
    public function index(): Response   
    {
        $categories = $this->entityManager->getRepository(Categorie::class)->findAll();


        return $this->render('vod/categories/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/admin/categories', name: 'categories_admin')]
    public function admin(): Response   
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $categories = $this->entityManager->getRepository(Categorie::class)->findAll();


        return $this->render('vod/categories/admin.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/categories/{libelle}', name: 'categories_show')]
    public function show(Categorie $categorie): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('vod/categories/show.html.twig', [
            'categorie' => $categorie,
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

            // return $this->redirectToRoute('categories_index');
        }

        return $this->render('vod/categories/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/categorie/{libelle}/edit', name: 'categories_edit', methods: ['GET', 'PUT'])]
    public function edit(Request $request, categorie $categorie): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(categorieType::class, $categorie, [
            // 'action' => $this->generateUrl('target_route'),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $libelle = $form->get('libelle')->getData();

            if ($image) {
                $fichier = strtolower(str_replace(array(' ', "'",":",";",",","\""), array('_', '_', '_', '_', '_', '_'), $libelle)) . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('categories_images_directory'),
                    $fichier
                );
                $categorie->setImage($fichier);

            }

            $this->entityManager->persist($categorie);
            $this->entityManager->flush();

            return $this->redirectToRoute('categories_index');
        }

        return $this->render('vod/categories/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/vod/series/categorie/{id}/delete', name: 'categories_delete', methods: ['GET', 'DELETE'])]
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('categorie_deletion' . $categorie->getId(), $request->request->get('csrf_token')))
        {
            $entityManager->remove($categorie);
            $entityManager->flush();
        }
        return $this->redirectToRoute('categories_index');
    }

}
