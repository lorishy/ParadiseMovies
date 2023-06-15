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
use App\Form\UserType;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

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


    #[Route('/admin/users', name: 'users_admin')]
    public function admin(): Response   
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $this->entityManager->getRepository(User::class)->findAll();


        return $this->render('users/admin.html.twig', [
            'users' => $users
        ]);
    }
    

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(
        FilmRepository $filmRepository,
        ActeurRepository $acteurRepository,
        SerieRepository $serieRepository,
        CategorieRepository $categorieRepository,
        UserRepository $userRepository
    ): Response 
    {
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
    public function show(User $user, Security $security): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $currentUser = $security->getUser();
    
        if (!$currentUser || ($currentUser->getId() !== $user->getId() && !$security->isGranted('ROLE_ADMIN'))) {
            throw new AccessDeniedException('Accès refusé.');
        }
        
        return $this->render('users/show.html.twig', [
            'user' => $user,
        ]);
    }

    

    #[Route('/vod/users/{id}/edit', name: 'users_edit', methods: ['GET', 'PUT'])]
    public function edit(Request $request, User $user, Security $security): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $currentUser = $security->getUser();
    
        if (!$currentUser || ($currentUser->getId() !== $user->getId() && !$security->isGranted('ROLE_ADMIN'))) {
            throw new AccessDeniedException('Accès refusé.');
        }

        $form = $this->createForm(UserType::class, $user, [
            // 'action' => $this->generateUrl('target_route'),
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $lastname = $form->get('lastname')->getData();
            $firstname = $form->get('firstname')->getData();

            if ($image) {
                $fichier = strtolower($firstname.'_'.$lastname) . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('users_images_directory'),
                    $fichier
                );
                $user->setImage($fichier);

            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('users_show', ['lastname' => $lastname, 'firstname' => $firstname]);
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
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

