<?php

namespace App\Controller;

use App\Repository\ActeurRepository;
use App\Entity\Acteur;
use App\Form\ActeurType;
use App\Form\SearchType;
use App\Model\SearchData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;


class ActeurController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // #[Route('/vod/acteurs', name: 'acteurs_index')]
    // public function index(): Response
    // {
    //     $acteurs = $this->entityManager->getRepository(Acteur::class)->findAll();

    //     return $this->render('acteurs/index.html.twig', [
    //         'acteurs' => $acteurs,
    //     ]);
    // }

    #[Route('/vod/acteurs', name: 'acteurs_index')]
    public function index( 
        ActeurRepository $acteurRepository, 
        PaginatorInterface $paginatorInterface,
        Request $request
    ): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $acteurs = $acteurRepository->findBySearch($searchData);

            return $this->render('acteurs/index.html.twig', [
                'form' => $form->createView(),
                'acteurs' => $acteurs
            ]);
        }

        return $this->render('acteurs/index.html.twig', [
            'form' => $form->createView(),
            'acteurs' => $acteurRepository->findActeurs($request->query->getInt('page', 1))
        ]);
    }



    #[Route('/vod/acteurs/{prenom}_{nom}', name: 'acteurs_show')]
    public function show(Acteur $acteur): Response
    {
        
        return $this->render('acteurs/show.html.twig', [
            'acteur' => $acteur,
        ]);
    }

    


    #[Route('/vod/acteurs-add', name: 'acteurs_add', methods: ['GET', 'POST'])]
    public function add(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $acteur = new Acteur();
        $form = $this->createForm(ActeurType::class, $acteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();
            $nom = $form->get('nom')->getData();
            $prenom = $form->get('prenom')->getData();

            if ($image) {
                $fichier = strtolower($prenom.'_'.$nom).'.'.$image->guessExtension();

                $image->move(
                    $this->getParameter('acteurs_images_directory'),
                    $fichier
                );
                $acteur->setImage($fichier);

            }

            $existingActeur = $this->entityManager->getRepository(Acteur::class)->findOneBy([
                'nom' => $nom,
                'prenom' => $prenom,
            ]);
    
            if ($existingActeur) {
                $this->addFlash('error', 'Le numéro d\'épisode existe déjà dans cette saison.');
            } else {
                $this->entityManager->persist($acteur);
                $this->entityManager->flush();
    
                $this->addFlash('success', 'L\'épisode a été ajouté avec succès.');
            }

            // return $this->redirectToRoute('acteurs_index');
        }

        return $this->render('acteurs/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/vod/acteurs/{prenom}_{nom}/edit', name: 'acteurs_edit', methods: ['GET', 'PUT'])]
    public function edit(Request $request, Acteur $acteur): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(ActeurType::class, $acteur, [
            // 'action' => $this->generateUrl('target_route'),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $nom = $form->get('nom')->getData();
            $prenom = $form->get('prenom')->getData();

            if ($image) {
                $fichier = strtolower($prenom.'_'.$nom).'.'.$image->guessExtension();

                $image->move(
                    $this->getParameter('acteurs_images_directory'),
                    $fichier
                );
                $acteur->setImage($fichier);

            }
            $this->entityManager->persist($acteur);
            $this->entityManager->flush();

            return $this->redirectToRoute('acteurs_index');
        }

        return $this->render('acteurs/edit.html.twig', [
            'acteur' => $acteur,
            'form' => $form->createView(),
        ]);
    }

    
    #[Route('/vod/acteurs/{prenom}_{nom}/delete', name: 'acteurs_delete', methods: ['GET', 'DELETE'])]
    public function delete(Request $request, Acteur $acteur, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('acteur_deletion' . $acteur->getId(), $request->request->get('csrf_token')))
        {
            $entityManager->remove($acteur);
            $entityManager->flush();
        }
        return $this->redirectToRoute('acteurs_index');
    }

}