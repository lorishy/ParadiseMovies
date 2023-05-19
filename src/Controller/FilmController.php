<?php

namespace App\Controller;

use App\Repository\FilmRepository;
use App\Entity\Film;
use App\Form\FilmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class FilmController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/vod/films', name: 'films_index')]
    public function index(): Response
    {
        $films = $this->entityManager->getRepository(Film::class)->findAll();

        return $this->render('vod/films/index.html.twig', [
            'films' => $films,
        ]);
    }

    #[Route('/vod/films/{titre}', name: 'films_show')]
    public function show(Film $film): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('vod/films/show.html.twig', [
            'film' => $film,
        ]);
    }


    #[Route('/vod/films-add', name: 'films_add', methods: ['GET', 'POST'])]
    public function add(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();
            $titre = $form->get('titre')->getData();

            if ($image) {
                $fichier = strtolower(str_replace(array(' ', "'"), array('_', '_'), $titre)) . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('films_images_directory'),
                    $fichier
                );
                $film->setImage($fichier);

            }

            $this->entityManager->persist($film);
            $this->entityManager->flush();

            return $this->redirectToRoute('films_index');
        }

        return $this->render('vod/films/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/vod/films/{titre}/edit', name: 'films_edit', methods: ['GET', 'PUT'])]
    public function edit(Request $request, Film $film): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(FilmType::class, $film, [
            // 'action' => $this->generateUrl('target_route'),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $titre = $form->get('titre')->getData();

            if ($image) {
                $fichier = strtolower(str_replace(array(' ', "'"), array('_', '_'), $titre)) . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('films_images_directory'),
                    $fichier
                );
                $film->setImage($fichier);

            }

            $this->entityManager->persist($film);
            $this->entityManager->flush();

            return $this->redirectToRoute('films_index');
        }

        return $this->render('vod/films/edit.html.twig', [
            'film' => $film,
            'form' => $form->createView(),
        ]);
    }

    
    #[Route('/vod/films/{titre}/delete', name: 'films_delete', methods: ['GET', 'DELETE'])]
    public function delete(Request $request, Film $film, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('film_deletion' . $film->getTitre(), $request->request->get('csrf_token')))
        {
            $entityManager->remove($film);
            $entityManager->flush();
        }
        return $this->redirectToRoute('films_index');
    }

    #[Route('/vod/films/search', name: 'films_search')]
    public function search(Request $request, FilmRepository $filmRepository) : Response
    {
        $searchTerm = $request->query->get('q');
        
        // Rechercher les films correspondant au terme de recherche
        $films = $filmRepository->findByTitre($searchTerm); // Supposons que la méthode findByTitle existe dans le repository

        return $this->render('vod/films/search.html.twig', [
            'films' => $films,
            'searchTerm' => $searchTerm,
        ]);
    }

}