<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Serie;
use App\Repository\FilmRepository;
use App\Repository\SerieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class VodController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/vod', name: 'app_vod')]
    public function index(): Response
    {
        $filmRepository = $this->entityManager->getRepository(Film::class);
        $films = $filmRepository->findBy([]);
    
        $serieRepository = $this->entityManager->getRepository(Serie::class);
        $series = $serieRepository->findBy([]);
    
        return $this->render('vod/index.html.twig', [
            'films' => $films,
            'series' => $series,
            'controller_name' => 'VodController',
        ]);
    }
}
