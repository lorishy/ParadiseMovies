<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\FilmRepository;
use App\Entity\Film;
use App\Form\AvisType;
use App\Form\FilmType;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class FilmController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    

    #[Route('/vod/films', name: 'films_index')]
    public function index( 
        FilmRepository $filmRepository, 
        PaginatorInterface $paginatorInterface,
        Request $request
    ): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $films = $filmRepository->findBySearch($searchData);

            return $this->render('vod/films/index.html.twig', [
                'form' => $form->createView(),
                'films' => $films
            ]);
        }

        return $this->render('vod/films/index.html.twig', [
            'form' => $form->createView(),
            'films' => $filmRepository->findFilms($request->query->getInt('page', 1))
        ]);
    }

    

    #[Route('/admin/films', name: 'films_admin')]
    public function admin(): Response   
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $films = $this->entityManager->getRepository(Film::class)->findAll();


        return $this->render('vod/films/admin.html.twig', [
            'films' => $films
        ]);
    }
    

    #[Route('/vod/films/{titre}', name: 'films_show')]
    public function show(Film $film, AvisRepository $avisRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        // Récupérer les avis associés au film
        $avis = $avisRepository->findBy(['film' => $film]);

        $user = $this->getUser();
    
        $avie = new Avis();
        $avie->setFilm($film);
        $avie->setUser($user);
        $form = $this->createForm(AvisType::class, $avie);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer l'avis
            $this->entityManager->persist($avie);
            $this->entityManager->flush();
    
            $this->addFlash('success', 'Votre avis a été ajouté avec succès.');
    
            // Rediriger vers la page du film
            return $this->redirectToRoute('films_show', ['titre' => $film->getTitre()]);
        }
    
        
        return $this->render('vod/films/show.html.twig', [
            'film' => $film,
            'avis' => $avis,
            'formAvie' => $form->createView(),
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
                $fichier = strtolower(str_replace(array(' ', "'",":",";",",","\""), array('_', '_', '_', '_', '_', '_'), $titre)) . '.' . $image->guessExtension();


                $image->move(
                    $this->getParameter('films_images_directory'),
                    $fichier
                );
                $film->setImage($fichier);

            }

            $this->entityManager->persist($film);
            $this->entityManager->flush();

            // return $this->redirectToRoute('films_index');
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
                $fichier = strtolower(str_replace(array(' ', "'",":",";",",","\""), array('_', '_', '_', '_', '_', '_'), $titre)) . '.' . $image->guessExtension();

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

}