<?php

namespace App\Controller\Admin;

use App\Repository\ActeurRepository;
use App\Entity\Acteur;
use App\Entity\Film;
use App\Entity\Serie;
use App\Entity\Catégorie;
use App\Repository\FilmRepository;
use App\Repository\SerieRepository;
use App\Repository\UserRepository;
use App\Repository\CategorieRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RegistrationFormType;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class UsersController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/administration', name: 'admin_admin')]
    public function index(UserRepository $usersRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $films = $this->entityManager->getRepository(Film::class)->findBy([], ['id' => 'asc']);

        $series = $this->entityManager->getRepository(Serie::class)->findBy([], ['id' => 'asc']);

        $users = $usersRepository->findBy([], ['id' => 'asc']);

        $acteurs = $this->entityManager->getRepository(Acteur::class)->findBy([], ['id' => 'asc']);

        return $this->render('admin/index.html.twig', compact('users', 'films', 'series', 'acteurs'));
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(
        FilmRepository $filmRepository,
        ActeurRepository $acteurRepository,
        SerieRepository $serieRepository,
        CategorieRepository $categorieRepository,
        UserRepository $userRepository
    ): Response {
        $filmCount = $filmRepository->count([]);
        $acteurCount = $acteurRepository->count([]);
        $serieCount = $serieRepository->count([]);
        $categorieCount = $categorieRepository->count([]);
        $userCount = $userRepository->count([]);

        return $this->render('admin/dashboard.html.twig', [
            'filmCount' => $filmCount,
            'acteurCount' => $acteurCount,
            'serieCount' => $serieCount,
            'categorieCount' => $categorieCount,
            'userCount' => $userCount,
        ]);
    }

    #[Route('/profil/{lastname}_{firstname}', name: 'users_show')]
    public function show(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('profil/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/admin/utilisateurs/{id}/delete', name: 'users_delete', methods: ['GET', 'DELETE'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('user_deletion' . $user->getId(), $request->request->get('csrf_token')))
        {
            $entityManager->remove($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_users_');
    }
}

