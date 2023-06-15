<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\SerieRepository;
use App\Entity\Serie;
use App\Entity\Episode;
use App\Form\AvisType;
use App\Form\SerieType;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Form\EpisodeType;
use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Common\Collections\ArrayCollection;


class SerieController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/vod/series', name: 'series_index')]
    public function index(
        serieRepository $serieRepository,
        PaginatorInterface $paginatorInterface,
        Request $request
    ): Response {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $series = $serieRepository->findBySearch($searchData);

            return $this->render('vod/series/index.html.twig', [
                'form' => $form->createView(),
                'series' => $series
            ]);
        }

        return $this->render('vod/series/index.html.twig', [
            'form' => $form->createView(),
            'series' => $serieRepository->findseries($request->query->getInt('page', 1))
        ]);
    }


    #[Route('/admin/series', name: 'series_admin')]
    public function admin(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $series = $this->entityManager->getRepository(Serie::class)->findAll();


        return $this->render('vod/series/admin.html.twig', [
            'series' => $series
        ]);
    }


    #[Route('/vod/series/{titre}', name: 'series_show')]
    public function show(Request $request, AvisRepository $avisRepository, Serie $serie): Response
    {
        $episodes = $serie->getEpisodes();

        // Récupérer les saisons uniques à partir des épisodes de la série
        $saisons = new ArrayCollection();
        foreach ($episodes as $episode) {
            $saison = $episode->getSaison();
            if (!$saisons->contains($saison)) {
                $saisons->add($saison);
            }
        }

        // Récupérer la saison sélectionnée à partir des paramètres de requête
        $selectedSeason = $request->query->get('saison');

        // Filtrer les épisodes de la saison sélectionnée
        $filteredEpisodes = $episodes->filter(function ($episode) use ($selectedSeason) {
            return $episode->getSaison() == $selectedSeason;
        });


        // Récupérer les avis associés a la série
        $avis = $avisRepository->findBy(['serie' => $serie]);

        $user = $this->getUser();

        $avie = new Avis();
        $avie->setSerie($serie);
        $avie->setUser($user);
        $form = $this->createForm(AvisType::class, $avie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer l'avis
            $this->entityManager->persist($avie);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre avis a été ajouté avec succès.');

            // Rediriger vers la page de la série
            return $this->redirectToRoute('series_show', ['titre' => $serie->getTitre(),  'saison' => 1]);
        }


        return $this->render('vod/series/show.html.twig', [
            'serie' => $serie,
            'saisons' => $saisons,
            'selectedSeason' => $selectedSeason,
            'episodes' => $episodes,
            'formAvie' => $form->createView(),
            'avis' => $avis,
        ]);
    }



    #[Route('/vod/series-add', name: 'series_add', methods: ['GET', 'POST'])]
    public function add(Request $request, Filesystem $filesystem): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $serie = new Serie();
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $titre = $form->get('titre')->getData();

            if ($image) {
                $fichier = strtolower(str_replace(array(' ', "'", ":", ";", ",", "\""), array('_', '_', '_', '_', '_', '_'), $titre)) . '.' . $image->guessExtension();

                //pour mettre dans un dossier au titre de la serie
                // $directory = $this->getParameter('series_images_directory') . '/' . str_replace(' ', '_', $serie->getTitre());

                // $filesystem->mkdir($directory); // Crée le dossier correspondant à la série

                $image->move(
                    //pour mettre dans un dossier au titre de la serie
                    // $directory,  
                    $this->getParameter('series_images_directory'),
                    $fichier
                );
                $serie->setImage($fichier);
            }
            $this->entityManager->persist($serie);
            $this->entityManager->flush();

            // return $this->redirectToRoute('series_index');
        }


        return $this->render('vod/series/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/vod/series/{titre}/edit', name: 'series_edit', methods: ['GET', 'PUT', 'POST'])]
    public function edit(Request $request, Serie $serie, Filesystem $filesystem): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(SerieType::class, $serie, [
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();
            $titre = $form->get('titre')->getData();

            if ($image) {
                $fichier = strtolower(str_replace(array(' ', "'", ":", ";", ",", "\""), array('_', '_', '_', '_', '_', '_'), $titre)) . '.' . $image->guessExtension();

                //pour mettre dans un dossier au titre de la serie
                // $directory = $this->getParameter('series_images_directory') . '/' . str_replace(' ', '_', $serie->getTitre());

                // $filesystem->mkdir($directory); // Crée le dossier correspondant à la série

                $image->move(
                    //pour mettre dans un dossier au titre de la serie
                    // $directory,  
                    $this->getParameter('series_images_directory'),
                    $fichier
                );
                $serie->setImage($fichier);
            }

            $this->entityManager->persist($serie);
            $this->entityManager->flush();

            return $this->redirectToRoute('series_index');
        }

        $episode = new Episode();
        $episode->setSerie($serie);
        $formEpisode = $this->createForm(EpisodeType::class, $episode);
        $formEpisode->handleRequest($request);

        if ($formEpisode->isSubmitted() && $formEpisode->isValid()) {
            // Vérifier si le numéro d'épisode existe déjà dans la saison
            $existingEpisode = $this->entityManager->getRepository(Episode::class)->findOneBy([
                'serie' => $serie,
                'saison' => $episode->getSaison(),
                'episode' => $episode->getEpisode(),
            ]);

            if ($existingEpisode) {
                $this->addFlash('error', 'Le numéro d\'épisode existe déjà dans cette saison.');
            } else {
                $this->entityManager->persist($episode);
                $this->entityManager->flush();

                $this->addFlash('success', 'L\'épisode a été ajouté avec succès.');
            }

            return $this->redirectToRoute('series_edit', ['titre' => $serie->getTitre(), 'saison' => 1]);
        }

        return $this->render('vod/series/edit.html.twig', [
            'serie' => $serie,
            'form' => $form->createView(),
            'formEpisode' => $formEpisode->createView(),
        ]);
    }



    #[Route('/vod/series/{id}/delete', name: 'series_delete', methods: ['DELETE'])]
    public function delete(Request $request, Serie $serie, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('serie_deletion' . $serie->getId(), $request->request->get('csrf_token'))) {
            // Supprimer les épisodes associés à la série
            $episodes = $serie->getEpisodes();
            foreach ($episodes as $episode) {
                $entityManager->remove($episode);
            }

            // Supprimer la série elle-même
            $entityManager->remove($serie);

            // Exécuter les suppressions en cascade
            $entityManager->flush();
        }

        return $this->redirectToRoute('series_index');
    }
}
