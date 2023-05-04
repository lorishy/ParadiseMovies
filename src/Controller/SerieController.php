<?php

namespace App\Controller;

use App\Repository\SerieRepository;
use App\Entity\Serie;
use App\Form\SerieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class SerieController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/vod/series', name: 'series_index')]
    public function index(): Response
    {
        $series = $this->entityManager->getRepository(Serie::class)->findAll();

        return $this->render('vod/series/index.html.twig', [
            'series' => $series,
        ]);
    }

    #[Route('/vod/series/{titre}', name: 'series_show')]
    public function show(Serie $serie): Response
    {

        return $this->render('vod/series/show.html.twig', [
            'serie' => $serie,
        ]);
    }


    #[Route('/vod/series-add', name: 'series_add', methods: ['GET', 'POST'])]
    public function add(Request $request): Response
    {
        $serie = new Serie(); 
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($serie);
            $this->entityManager->flush();

            return $this->redirectToRoute('series_index');
        }

        return $this->render('vod/series/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/vod/series/{titre}/edit', name: 'series_edit', methods: ['GET', 'PUT'])]
    public function edit(Request $request, Serie $serie): Response
    {
        $form = $this->createForm(SerieType::class, $serie, [
            // 'action' => $this->generateUrl('target_route'),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($serie);
            $this->entityManager->flush();

            return $this->redirectToRoute('series_index');
        }

        return $this->render('vod/series/edit.html.twig', [
            'serie' => $serie,
            'form' => $form->createView(),
        ]);
    }

    
    #[Route('/vod/series/{titre}/delete', name: 'series_delete', methods: ['GET', 'DELETE'])]
    public function delete(Serie $serie, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($serie);
        $entityManager->flush();

        return $this->redirectToRoute('series_index');
    }
}