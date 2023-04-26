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
    public function show(): Response
    {
        $films = $this->entityManager->getRepository(Film::class)->findAll();

        return $this->render('vod/films/show.html.twig', [
            'films' => $films,
        ]);
    }

    #[Route('/vod/films/new', name: 'films_new')]
    public function new(Request $request): Response
    {
        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($film);
            $this->entityManager->flush();

            return $this->redirectToRoute('films_index');
        }

        return $this->render('vod/films/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}